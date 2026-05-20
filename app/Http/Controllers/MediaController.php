<?php

namespace App\Http\Controllers;

use App\Models\PostMedia;

class MediaController extends Controller
{
    public function index()
    {
        $media = PostMedia::whereHas('post', fn($q) => $q->where('user_id', auth()->id()))
            ->with('post')
            ->latest()
            ->paginate(20);

        return view('media.index', compact('media'));
    }
}
