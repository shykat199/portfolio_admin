<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'logo_path', 'favicon_path', 'linkedin_url', 'github_url', 'facebook_url', 'instagram_url', 'x_url',
    'whatsapp_number', 'hero_prefix', 'hero_words', 'hero_headline', 'hero_highlight', 'hero_supporting',
    'seo_title', 'seo_description', 'seo_keywords', 'about_title', 'about_story', 'about_photo_path', 'about_photo_alt',
    'about_photo_width', 'about_photo_height', 'about_reasons_title', 'about_reasons',
])]
class SiteSetting extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'about_reasons' => 'array',
            'hero_words' => 'array',
            'seo_keywords' => 'array',
            'about_photo_width' => 'integer',
            'about_photo_height' => 'integer',
        ];
    }

    /**
     * The one settings row, created with empty defaults the first time.
     */
    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'about_title' => 'About',
            'about_story' => '',
            'about_reasons_title' => 'Why you can rely on me',
            'about_reasons' => [],
        ]);
    }

    public function url(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    /**
     * The about text as separate paragraphs (split on blank lines).
     *
     * @return list<string>
     */
    public function aboutParagraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $this->about_story))));
    }
}
