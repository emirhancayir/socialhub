<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SocialAccount;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $accounts = SocialAccount::where('user_id', auth()->id())->get();

        $posts = Post::where('user_id', auth()->id())
            ->with(['media', 'results'])
            ->latest()
            ->paginate(10);

        return view('dashboard', compact('accounts', 'posts'));
    }
}
