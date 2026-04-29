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
        $request->validate([
            'content'      => 'nullable|string|max:2200',
            'title'        => 'nullable|string|max:150',
            'platforms'    => 'required|array|min:1',
            'platforms.*'  => 'in:instagram,twitter,tiktok,facebook',
            'media'        => 'nullable|array',
            'media.*'      => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:102400',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $scheduledAt = $request->scheduled_at ? now()->parse($request->scheduled_at) : null;

        $post = Post::create([
            'user_id'      => auth()->id(),
            'content'      => $request->content,
            'title'        => $request->title,
            'platforms'    => $request->platforms,
            'status'       => 'draft',
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

    public function destroy(Post $post)
    {
        abort_if($post->user_id !== auth()->id(), 403);
        $post->delete();
        return back()->with('success', 'Post silindi.');
    }
}
