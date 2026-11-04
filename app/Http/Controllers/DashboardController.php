<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Post;
use App\PostStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function __invoke(Request $request): View
    {
        return view('dashboard', [
            'user' => $request->user(),
            'publishedCount' => Post::query()->published()->count(),
            'draftCount' => Post::query()->where('status', PostStatus::Draft)->count(),
            'latestPosts' => Post::query()->latest('updated_at')->limit(3)->get(),
            'latestMessages' => Message::query()->latest()->limit(4)->get(),
        ]);
    }
}
