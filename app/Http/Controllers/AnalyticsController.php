<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostResult;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalPosts     = Post::where('user_id', $userId)->count();
        $publishedPosts = Post::where('user_id', $userId)->where('status', 'published')->count();
        $failedPosts    = Post::where('user_id', $userId)->where('status', 'failed')->count();
        $draftPosts     = Post::where('user_id', $userId)->where('status', 'draft')->count();

        // Platform stats: count published/failed PostResult records for user's posts
        $platforms = ['instagram', 'twitter', 'tiktok'];
        $platformStats = [];
        foreach ($platforms as $platform) {
            $platformStats[$platform] = [
                'published' => PostResult::whereHas('post', fn($q) => $q->where('user_id', $userId))
                    ->where('platform', $platform)
                    ->where('status', 'success')
                    ->count(),
                'failed' => PostResult::whereHas('post', fn($q) => $q->where('user_id', $userId))
                    ->where('platform', $platform)
                    ->where('status', 'failed')
                    ->count(),
            ];
        }

        // Daily stats: last 14 days, published posts per day
        $dailyStats = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyStats[] = [
                'date'  => $date->format('d M'),
                'count' => Post::where('user_id', $userId)
                    ->where('status', 'published')
                    ->whereDate('updated_at', $date)
                    ->count(),
            ];
        }

        return view('analytics', compact(
            'totalPosts', 'publishedPosts', 'failedPosts', 'draftPosts',
            'platformStats', 'dailyStats'
        ));
    }
}
