<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends Controller
{
    /**
     * The logo, favicon, social links and About section for the website.
     */
    public function __invoke(): JsonResponse
    {
        $settings = SiteSetting::current();
        $photo = $settings->url($settings->about_photo_path);

        return response()->json(['data' => [
            'logo' => $settings->url($settings->logo_path),
            'favicon' => $settings->url($settings->favicon_path),
            'social' => [
                'linkedin' => $settings->linkedin_url,
                'github' => $settings->github_url,
                'facebook' => $settings->facebook_url,
                'instagram' => $settings->instagram_url,
                'x' => $settings->x_url,
            ],
            'whatsapp' => $settings->whatsapp_number,
            'hero' => $settings->hero_headline ? [
                'prefix' => $settings->hero_prefix,
                'words' => $settings->hero_words ?? [],
                'headline' => $settings->hero_headline,
                'highlight' => $settings->hero_highlight,
                'supporting' => $settings->hero_supporting,
            ] : null,
            'seo' => $settings->seo_title ? [
                'title' => $settings->seo_title,
                'description' => $settings->seo_description,
                'keywords' => $settings->seo_keywords ?? [],
            ] : null,
            'about' => [
                'title' => $settings->about_title,
                'story' => $settings->aboutParagraphs(),
                'photo' => $photo ? [
                    'src' => $photo,
                    'alt' => (string) $settings->about_photo_alt,
                    'width' => $settings->about_photo_width,
                    'height' => $settings->about_photo_height,
                ] : null,
                'reasonsTitle' => $settings->about_reasons_title,
                'reasons' => $settings->about_reasons,
            ],
        ]]);
    }
}
