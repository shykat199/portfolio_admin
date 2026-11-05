<?php

namespace Tests\Feature;

use App\Services\WebpImageStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebpImageStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_jpg_and_png_uploads_are_stored_as_webp(): void
    {
        $store = $this->app->make(WebpImageStore::class);

        foreach (['photo.jpg', 'diagram.png'] as $name) {
            $path = $store->store(UploadedFile::fake()->image($name, 400, 300), 'work');

            $this->assertStringStartsWith('work/', $path);
            $this->assertStringEndsWith('.webp', $path);
            $this->assertSame(IMAGETYPE_WEBP, getimagesize(Storage::disk('public')->path($path))[2]);
        }
    }

    public function test_large_images_are_shrunk_to_the_configured_maximum(): void
    {
        config(['webpbolt.max_dimension' => 1600]);

        $path = $this->app->make(WebpImageStore::class)->store(UploadedFile::fake()->image('wide.jpg', 3200, 1000), 'blog');

        [$width, $height] = getimagesize(Storage::disk('public')->path($path));
        $this->assertSame([1600, 500], [$width, $height]);
    }

    public function test_svg_and_gif_uploads_are_stored_unchanged(): void
    {
        $store = $this->app->make(WebpImageStore::class);

        $svgPath = $store->store(UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>'), 'tech');
        $gifPath = $store->store(UploadedFile::fake()->image('loop.gif', 50, 50), 'blog/content');

        $this->assertStringEndsWith('.svg', $svgPath);
        $this->assertStringEndsWith('.gif', $gifPath);
        Storage::disk('public')->assertExists([$svgPath, $gifPath]);
    }
}
