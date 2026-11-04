<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'title', 'slug', 'is_demo', 'category', 'year', 'service', 'problem', 'solution', 'result',
    'overview', 'client_problem', 'built', 'tech', 'results', 'cover_path', 'cover_alt', 'screenshots',
    'seo_title', 'meta_description', 'meta_keywords', 'is_active', 'sort_order',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
            'built' => 'array',
            'tech' => 'array',
            'results' => 'array',
            'screenshots' => 'array',
            'meta_keywords' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Projects shown on the website, in their display order.
     *
     * @param  Builder<Project>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null);
    }

    /**
     * Screenshots with public URLs.
     *
     * @return list<array{src: string, alt: string, caption: string}>
     */
    public function screenshotUrls(): array
    {
        return array_map(fn (array $shot): array => [
            'src' => Storage::disk('public')->url($shot['path']),
            'alt' => $shot['alt'] ?? '',
            'caption' => $shot['caption'] ?? '',
        ], $this->screenshots ?? []);
    }

    /**
     * What search engines see. Empty fields fall back to the title (marked
     * as a demo where relevant), the overview and the tech used.
     *
     * @return array{title: string, description: string, keywords: list<string>}
     */
    public function seo(): array
    {
        return [
            'title' => $this->seo_title ?: $this->title.($this->is_demo ? ' (demo)' : ''),
            'description' => $this->meta_description ?: Str::limit($this->overview, 157),
            'keywords' => $this->meta_keywords ?: [...$this->tech, $this->category],
        ];
    }
}
