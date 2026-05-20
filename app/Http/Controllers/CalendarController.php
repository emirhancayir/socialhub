<?php

namespace App\Http\Controllers;

use App\Models\Post;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

    public function events()
    {
        $posts = Post::where('user_id', auth()->id())
            ->whereNotNull('scheduled_at')
            ->orWhere(function ($q) {
                $q->where('user_id', auth()->id())
                  ->where('status', 'published');
            })
            ->get();

        $colorMap = [
            'published'  => '#22c55e',
            'failed'     => '#ef4444',
            'draft'      => '#6c63ff',
            'publishing' => '#f59e0b',
        ];

        $events = $posts->map(function ($post) use ($colorMap) {
            $platforms = is_array($post->platforms) ? $post->platforms : [];
            $title = implode(' + ', array_map(fn($p) => ucfirst($p === 'twitter' ? 'X' : $p), $platforms));

            return [
                'id'     => $post->id,
                'title'  => $title ?: 'Post #' . $post->id,
                'start'  => ($post->scheduled_at ?? $post->created_at)->toDateString(),
                'color'  => $colorMap[$post->status] ?? '#6c63ff',
                'status' => $post->status,
            ];
        });

        return response()->json($events);
    }
}
