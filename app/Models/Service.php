<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'slug', 'for_who', 'price', 'price_suffix', 'billed_monthly', 'time_label', 'time',
    'includes', 'heading', 'intro', 'story', 'ideal_for', 'faq', 'related_projects',
    'seo_title', 'meta_description', 'meta_keywords', 'is_active', 'sort_order',
])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'billed_monthly' => 'boolean',
            'includes' => 'array',
            'ideal_for' => 'array',
            'faq' => 'array',
            'related_projects' => 'array',
            'meta_keywords' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Services shown on the website, in their display order.
     *
     * @param  Builder<Service>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The story as separate paragraphs (split on blank lines).
     *
     * @return list<string>
     */
    public function storyParagraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', $this->story))));
    }

    /**
     * What search engines see. Empty fields fall back to the heading, intro
     * and name.
     *
     * @return array{title: string, description: string, keywords: list<string>}
     */
    public function seo(): array
    {
        return [
            'title' => $this->seo_title ?: $this->heading,
            'description' => $this->meta_description ?: $this->intro,
            'keywords' => $this->meta_keywords ?: [$this->name],
        ];
    }
}
