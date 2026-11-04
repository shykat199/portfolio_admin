<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use App\Models\Service;
use App\PostStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    use RefreshesFrontend;

    /**
     * List every post, newest first.
     */
    public function index(): View
    {
        return view('posts.index', [
            'posts' => Post::query()->latest('updated_at')->paginate(15),
        ]);
    }

    /**
     * Show the form for a new post.
     */
    public function create(): View
    {
        return view('posts.form', [
            'post' => new Post(['status' => PostStatus::Draft, 'tags' => [], 'faq' => []]),
            'services' => $this->serviceOptions(),
        ]);
    }

    /**
     * Save a new post.
     */
    public function store(PostRequest $request): RedirectResponse
    {
        $post = Post::create($this->attributes($request));

        $this->refreshFrontend('posts');

        return redirect()->route('posts.edit', $post)->with('toast', [
            'type' => 'success',
            'message' => $post->isLive() ? __('Post published.') : __('Draft saved.'),
        ]);
    }

    /**
     * Show the form for editing a post.
     */
    public function edit(Post $post): View
    {
        return view('posts.form', [
            'post' => $post,
            'services' => $this->serviceOptions(),
        ]);
    }

    /**
     * Save changes to a post.
     */
    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $previousCover = $post->cover_path;

        $post->update($this->attributes($request, $post));

        if ($previousCover && $previousCover !== $post->cover_path) {
            Storage::disk('public')->delete($previousCover);
        }

        $this->refreshFrontend('posts');

        return redirect()->route('posts.edit', $post)->with('toast', [
            'type' => 'success',
            'message' => __('Changes saved.'),
        ]);
    }

    /**
     * Delete a post and its cover image.
     */
    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_path) {
            Storage::disk('public')->delete($post->cover_path);
        }

        $post->delete();

        $this->refreshFrontend('posts');

        return redirect()->route('posts.index')->with('toast', [
            'type' => 'success',
            'message' => __('Post deleted.'),
        ]);
    }

    /**
     * Services a post can link to, as slug => name.
     *
     * @return Collection<string, string>
     */
    private function serviceOptions(): Collection
    {
        return Service::query()->orderBy('sort_order')->pluck('name', 'slug');
    }

    /**
     * The validated fields to save, with the uploaded cover stored and the
     * publish date set the first time a post goes live.
     *
     * @return array<string, mixed>
     */
    private function attributes(PostRequest $request, ?Post $post = null): array
    {
        $attributes = $request->safe()->except('cover');

        if ($request->hasFile('cover')) {
            $attributes['cover_path'] = $request->file('cover')->store('blog', 'public');
        }

        $isPublishing = $request->enum('status', PostStatus::class) === PostStatus::Published;
        $attributes['published_at'] = $request->date('published_at')
            ?? ($isPublishing ? ($post?->published_at ?? now()) : $post?->published_at);

        return $attributes;
    }
}
