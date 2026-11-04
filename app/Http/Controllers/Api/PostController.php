<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    /**
     * Published posts, newest first.
     */
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::query()->published()->latest('published_at')->get(),
        );
    }

    /**
     * One published post. Drafts and scheduled posts are not found.
     */
    public function show(string $slug): PostResource
    {
        return new PostResource(
            Post::query()->published()->where('slug', $slug)->firstOrFail(),
        );
    }
}
