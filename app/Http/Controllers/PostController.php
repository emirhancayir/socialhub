<?php

namespace App\Http\Controllers;

use App\Jobs\PublishPost;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\SocialAccount;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function create()
    {
        $accounts = SocialAccount::where('user_id', auth()->id())->get()->groupBy('platform');
        return view('posts.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        // If saving as draft, skip platform and scheduling requirements
        $saveDraft = $request->has('save_draft');

        $request->validate([
            'content'      => 'nullable|string|max:2200',
            'title'        => 'nullable|string|max:150',
            'platforms'    => ($saveDraft ? 'nullable' : 'required') . '|array|min:1',
            'platforms.*'  => 'in:instagram,twitter,tiktok,facebook',
            'media'        => 'nullable|array',
            'media.*'      => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:256000',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($saveDraft) {
            return $this->saveDraft($request);
        }

        $scheduledAt = $request->scheduled_at ? now()->parse($request->scheduled_at) : null;
        $isScheduled = $scheduledAt && $scheduledAt->isFuture();

        $post = Post::create([
            'user_id'      => auth()->id(),
            'content'      => $request->content,
            'title'        => $request->title,
            'platforms'    => $request->platforms,
            'status'       => $isScheduled ? 'draft' : 'publishing',
            'scheduled_at' => $scheduledAt,
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path    = $file->store('posts/' . $post->id, 'public');
                $isVideo = str_starts_with($file->getMimeType(), 'video');

                PostMedia::create([
                    'post_id'   => $post->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $isVideo ? 'video' : 'image',
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        if ($scheduledAt && $scheduledAt->isFuture()) {
            PublishPost::dispatch($post)->delay($scheduledAt);
            return redirect()->route('dashboard')->with('success', 'Post zamanlandı! ' . $scheduledAt->format('d.m.Y H:i') . ' tarihinde yayınlanacak.');
        }

        PublishPost::dispatch($post);
        return redirect()->route('dashboard')->with('success', 'Post kuyruğa alındı, yayınlanıyor!');
    }

    protected function saveDraft(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'content'   => 'nullable|string|max:2200',
            'title'     => 'nullable|string|max:150',
            'platforms' => 'nullable|array',
            'platforms.*' => 'in:instagram,twitter,tiktok,facebook',
            'media'     => 'nullable|array',
            'media.*'   => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:256000',
        ]);

        $post = Post::create([
            'user_id'   => auth()->id(),
            'content'   => $request->content,
            'title'     => $request->title,
            'platforms' => $request->platforms ?? [],
            'status'    => 'draft',
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path    = $file->store('posts/' . $post->id, 'public');
                $isVideo = str_starts_with($file->getMimeType(), 'video');

                PostMedia::create([
                    'post_id'   => $post->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $isVideo ? 'video' : 'image',
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Taslak kaydedildi.');
    }

    public function retry(Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403);

        $post->update(['status' => 'publishing']);
        PublishPost::dispatch($post);

        return redirect()->route('dashboard')->with('success', 'Post yeniden yayınlanmak üzere kuyruğa alındı.');
    }

    public function destroy(Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403);
        $post->delete();
        return back()->with('success', 'Post silindi.');
    }

    public function progress(Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403);

        $path = storage_path('app/progress/post_' . $post->id . '.json');
        if (!file_exists($path)) {
            return response()->json(['stage' => 'queued', 'percent' => 0, 'message' => 'Kuyrukta...', 'status' => $post->status]);
        }

        $data = json_decode(file_get_contents($path), true) ?? [];
        return response()->json([
            'stage'   => $data['stage']   ?? 'unknown',
            'percent' => $data['percent'] ?? 0,
            'message' => $data['message'] ?? '',
            'status'  => $post->status,
        ]);
    }
}
