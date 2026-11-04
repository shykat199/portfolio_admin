<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Import the home page questions that were on the website before the
     * admin panel existed. Running it again updates them, not duplicates.
     */
    public function run(): void
    {
        $faqs = json_decode(file_get_contents(__DIR__.'/faqs.json'), true, flags: JSON_THROW_ON_ERROR);

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
