<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Import the jobs that were on the website before the admin panel existed.
     */
    public function run(): void
    {
        $jobs = json_decode(file_get_contents(__DIR__.'/experience.json'), true, flags: JSON_THROW_ON_ERROR);

        foreach ($jobs as $job) {
            Experience::updateOrCreate(['company' => $job['company'], 'role' => $job['role']], $job);
        }
    }
}
