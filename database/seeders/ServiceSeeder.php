<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Import the three service packages that were on the website before the
     * admin panel existed. Running it again updates them, not duplicates.
     */
    public function run(): void
    {
        $services = json_decode(file_get_contents(__DIR__.'/services.json'), true, flags: JSON_THROW_ON_ERROR);

        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
