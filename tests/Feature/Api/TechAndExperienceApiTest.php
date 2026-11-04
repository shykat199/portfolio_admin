<?php

namespace Tests\Feature\Api;

use App\Models\Experience;
use App\Models\TechItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechAndExperienceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_tech_stack_lists_shown_items_in_order_with_logos(): void
    {
        TechItem::factory()->create(['name' => 'Second', 'sort_order' => 2]);
        TechItem::factory()->create(['name' => 'First', 'sort_order' => 1, 'logo_path' => 'tech/first.svg']);
        TechItem::factory()->hidden()->create(['sort_order' => 0]);

        $this->getJson(route('api.tech.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0', ['name' => 'First', 'logo' => url('storage/tech/first.svg')])
            ->assertJsonPath('data.1.logo', null);
    }

    public function test_experience_lists_shown_jobs_in_order(): void
    {
        Experience::factory()->create(['company' => 'Older', 'sort_order' => 2]);
        Experience::factory()->create(['company' => 'Current', 'end' => null, 'sort_order' => 1]);
        Experience::factory()->hidden()->create(['sort_order' => 0]);

        $this->getJson(route('api.experience.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.company', 'Current')
            ->assertJsonPath('data.0.end', null);
    }
}
