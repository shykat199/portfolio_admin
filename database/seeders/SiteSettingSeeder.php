<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SiteSettingSeeder extends Seeder
{
    /**
     * Import the hero, home page SEO and About section that were on the website before the admin
     * panel existed. The logo and favicon start empty, so the website keeps
     * its built-in ones until new images are uploaded.
     */
    public function run(): void
    {
        $settings = json_decode(file_get_contents(__DIR__.'/settings.json'), true, flags: JSON_THROW_ON_ERROR);

        $photo = 'about/shykat-roy-portrait.jpg';
        Storage::disk('public')->put($photo, file_get_contents(__DIR__.'/about/shykat-roy-portrait.jpg'));

        SiteSetting::current()->update([
            ...$settings,
            'about_photo_path' => $photo,
            'linkedin_url' => 'https://www.linkedin.com/in/shykat-roy/',
            'github_url' => 'https://github.com/shykat199',
            'whatsapp_number' => '8801317120335',
        ]);
    }
}
