<?php

namespace App\Services;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Shykat\WebpBolt\Encoders\WebpEncoder;
use Shykat\WebpBolt\ImageProcessor;
use Shykat\WebpBolt\Transforms\AutoOrient;

/**
 * Stores uploaded images on a disk, converting JPG, PNG and BMP files to WebP
 * with WebpBolt first. Other files (SVG, WebP, animated GIF) are stored as-is,
 * and if a conversion fails the original upload is stored instead.
 */
class WebpImageStore
{
    /**
     * Image types WebpBolt converts. GIF is left out so animations survive.
     *
     * @var list<int>
     */
    private const CONVERTIBLE_TYPES = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP];

    public function __construct(private Container $container) {}

    /**
     * Store the upload in the folder and return its path on the disk.
     */
    public function store(UploadedFile $file, string $folder, string $disk = 'public'): string
    {
        $sourcePath = $file->getRealPath();

        if (! $sourcePath || ! $this->isConvertible($sourcePath)) {
            return $file->store($folder, $disk);
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'webpbolt');

        try {
            $this->container->make(ImageProcessor::class)
                ->addTransform(new AutoOrient($sourcePath))
                ->save($sourcePath, $temporaryPath, new WebpEncoder((int) config('webpbolt.quality', 80)));

            return Storage::disk($disk)->putFileAs($folder, new File($temporaryPath), Str::random(40).'.webp');
        } catch (RuntimeException) {
            return $file->store($folder, $disk);
        } finally {
            @unlink($temporaryPath);
        }
    }

    private function isConvertible(string $path): bool
    {
        $info = @getimagesize($path);

        return $info !== false && in_array($info[2], self::CONVERTIBLE_TYPES, true);
    }
}
