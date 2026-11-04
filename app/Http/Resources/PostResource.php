<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array. The list endpoint leaves out the
     * article body and FAQ to keep it light.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $updated = $this->updated_at?->gt($this->published_at?->copy()->addDay()) ? $this->updated_at : null;

        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'seo' => $this->seo(),
            'description' => $this->description,
            'category' => $this->category,
            'tags' => $this->tags,
            'published' => $this->published_at?->toDateString(),
            'updated' => $updated?->toDateString(),
            'readingMinutes' => $this->readingMinutes(),
            'wordCount' => $this->wordCount(),
            'cover' => $this->cover_url ? ['src' => $this->cover_url, 'alt' => $this->cover_alt ?? ''] : null,
            'service' => $this->service,
            $this->mergeWhen($request->routeIs('api.posts.show'), fn (): array => Post::renderMarkdown($this->body)),
            'faq' => $this->when($request->routeIs('api.posts.show'), $this->faq),
        ];
    }
}
