<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ProjectSeeder extends Seeder
{
    /**
     * Import the case studies that were on the website before the admin
     * panel existed, with their images. Running it again updates them.
     */
    public function run(): void
    {
        $projects = json_decode(file_get_contents(__DIR__.'/projects.json'), true, flags: JSON_THROW_ON_ERROR);

        foreach ($projects as $project) {
            // Screenshots get their own copies, so removing one never removes the cover.
            $screenshots = array_map(fn (array $shot): array => [
                'path' => $this->copyImage($shot['file'], 'work/screens'),
                'alt' => $shot['alt'],
                'caption' => $shot['caption'],
            ], $project['screenshots']);

            Project::updateOrCreate(['slug' => $project['slug']], [
                ...Arr::except($project, ['cover_file', 'screenshots']),
                'cover_path' => $this->copyImage($project['cover_file'], 'work'),
                'screenshots' => $screenshots,
            ]);
        }
    }

    private function copyImage(string $file, string $folder): string
    {
        $path = "$folder/$file";
        Storage::disk('public')->put($path, file_get_contents(__DIR__.'/work-images/'.$file));

        return $path;
    }
}
