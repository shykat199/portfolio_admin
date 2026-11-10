<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSettingRequest;
use App\Models\SiteSetting;
use App\Services\WebpImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    use RefreshesFrontend;

    public function __construct(private WebpImageStore $images) {}

    /**
     * Show the brand and About settings.
     */
    public function edit(): View
    {
        return view('settings.site', ['settings' => SiteSetting::current()]);
    }

    /**
     * Save the settings, storing, replacing or removing uploaded images.
     */
    public function update(SiteSettingRequest $request): RedirectResponse
    {
        $settings = SiteSetting::current();
        $attributes = $request->safe()->only([
            'linkedin_url', 'github_url', 'facebook_url', 'instagram_url', 'x_url', 'whatsapp_number',
            'hero_prefix', 'hero_words', 'hero_headline', 'hero_highlight', 'hero_supporting',
            'seo_title', 'seo_description', 'seo_keywords',
            'person_name', 'person_job_title', 'person_description', 'person_email', 'person_phone', 'person_website',
            'person_street_address', 'person_postal_code', 'person_city', 'person_country', 'person_skills', 'person_languages',
            'about_title', 'about_story', 'about_photo_alt', 'about_reasons_title', 'about_reasons',
            'hidden_sections',
        ]);

        $attributes += $this->image($request, $settings, 'logo', 'logo_path', 'brand');
        $attributes += $this->image($request, $settings, 'favicon', 'favicon_path', 'brand', convertToWebp: false);
        $attributes += $this->image($request, $settings, 'resume', 'resume_path', 'resume', convertToWebp: false);

        if ($request->hasFile('about_photo')) {
            $this->delete($settings->about_photo_path);
            $photoPath = $this->images->store($request->file('about_photo'), 'about');
            [$width, $height] = getimagesize(Storage::disk('public')->path($photoPath)) ?: [null, null];
            $attributes += [
                'about_photo_path' => $photoPath,
                'about_photo_width' => $width,
                'about_photo_height' => $height,
            ];
        }

        $settings->update($attributes);

        $this->refreshFrontend('settings');

        return redirect()->route('site-settings.edit')->with('toast', [
            'type' => 'success',
            'message' => __('Site settings saved.'),
        ]);
    }

    /**
     * Handle one optional upload (an image or the résumé) with a "remove" checkbox.
     *
     * @return array<string, string|null>
     */
    private function image(SiteSettingRequest $request, SiteSetting $settings, string $field, string $column, string $folder, bool $convertToWebp = true): array
    {
        $file = $request->file($field);

        if (! $file instanceof UploadedFile && ! $request->boolean("remove_$field")) {
            return [];
        }

        $this->delete($settings->{$column});

        if (! $file instanceof UploadedFile) {
            return [$column => null];
        }

        return [$column => $convertToWebp ? $this->images->store($file, $folder) : $file->store($folder, 'public')];
    }

    private function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
