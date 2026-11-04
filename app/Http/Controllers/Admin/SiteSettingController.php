<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    use RefreshesFrontend;

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
            'about_title', 'about_story', 'about_photo_alt', 'about_reasons_title', 'about_reasons',
        ]);

        $attributes += $this->image($request, $settings, 'logo', 'logo_path', 'brand');
        $attributes += $this->image($request, $settings, 'favicon', 'favicon_path', 'brand');

        if ($request->hasFile('about_photo')) {
            $this->delete($settings->about_photo_path);
            $photo = $request->file('about_photo');
            [$width, $height] = getimagesize($photo->getRealPath()) ?: [null, null];
            $attributes += [
                'about_photo_path' => $photo->store('about', 'public'),
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
     * Handle one optional image field with a "remove" checkbox.
     *
     * @return array<string, string|null>
     */
    private function image(SiteSettingRequest $request, SiteSetting $settings, string $field, string $column, string $folder): array
    {
        $file = $request->file($field);

        if (! $file instanceof UploadedFile && ! $request->boolean("remove_$field")) {
            return [];
        }

        $this->delete($settings->{$column});

        return [$column => $file instanceof UploadedFile ? $file->store($folder, 'public') : null];
    }

    private function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
