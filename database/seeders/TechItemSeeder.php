<?php

namespace Database\Seeders;

use App\Models\TechItem;
use Illuminate\Database\Seeder;

class TechItemSeeder extends Seeder
{
    /**
     * Import the tech stack that was on the website before the admin panel
     * existed. Logos can be uploaded afterwards in the admin panel.
     */
    public function run(): void
    {
        $items = json_decode(file_get_contents(__DIR__.'/tech.json'), true, flags: JSON_THROW_ON_ERROR);

        foreach ($items as $item) {
            TechItem::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
