<?php

namespace App\Models;

use App\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;

#[Fillable([
    'title', 'slug', 'seo_title', 'meta_description', 'meta_keywords', 'description', 'category', 'tags', 'cover_path', 'cover_alt',
    'body', 'faq', 'service', 'status', 'published_at',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'meta_keywords' => 'array',
            'faq' => 'array',
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * Only posts that are published and whose publish date has arrived.
     *
     * @param  Builder<Post>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isLive(): bool
    {
        return $this->status === PostStatus::Published && $this->published_at?->isPast();
    }

    /**
     * Public URL of the cover image, if one has been uploaded.
     *
     * @return Attribute<string|null, never>
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null);
    }

    /**
     * Turn the Markdown article into safe HTML, plus the list of sections
     * (its main headings) for the "On this page" contents.
     *
     * Raw HTML in the Markdown is escaped and unsafe links are removed. A
     * "# heading" becomes a large h2, so the page keeps a single h1 (the title).
     *
     * @return array{html: string, toc: list<array{id: string, text: string}>}
     */
    public static function renderMarkdown(string $markdown): array
    {
        $html = Str::markdown($markdown, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 50,
            'heading_permalink' => [
                'apply_id_to_heading' => true,
                'insert' => 'none',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'min_heading_level' => 1,
                'max_heading_level' => 4,
            ],
            'external_link' => [
                'internal_hosts' => array_filter([parse_url((string) config('blog.frontend_url'), PHP_URL_HOST)]),
                'open_in_new_window' => true,
                'noopener' => 'external',
                'noreferrer' => 'external',
            ],
        ], [new HeadingPermalinkExtension, new ExternalLinkExtension]);

        $html = preg_replace(['/<h1(\s|>)/', '/<\/h1>/'], ['<h2 data-level="1"$1', '</h2>'], $html);

        preg_match_all('/<h2 [^>]*id="([^"]+)"[^>]*>(.*?)<\/h2>/s', $html, $matches, PREG_SET_ORDER);
        $toc = array_map(fn (array $match): array => [
            'id' => $match[1],
            'text' => html_entity_decode(strip_tags($match[2]), ENT_QUOTES | ENT_HTML5),
        ], $matches);

        return ['html' => trim($html), 'toc' => $toc];
    }

    /**
     * What search engines and social sites see. Empty fields fall back to
     * the title, the summary and the topics.
     *
     * @return array{title: string, description: string, keywords: list<string>}
     */
    public function seo(): array
    {
        return [
            'title' => $this->seo_title ?: $this->title,
            'description' => $this->meta_description ?: $this->description,
            'keywords' => $this->meta_keywords ?: $this->tags,
        ];
    }

    public function wordCount(): int
    {
        return Str::wordCount(strip_tags($this->body));
    }

    /** About 200 words a minute, never less than one. */
    public function readingMinutes(): int
    {
        return max(1, (int) round($this->wordCount() / 200));
    }
}
