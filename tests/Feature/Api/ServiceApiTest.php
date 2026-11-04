<?php

namespace Tests\Feature\Api;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_list_shows_active_services_in_display_order(): void
    {
        Service::factory()->create(['name' => 'Second', 'sort_order' => 2]);
        Service::factory()->create(['name' => 'First', 'sort_order' => 1]);
        Service::factory()->hidden()->create(['name' => 'Hidden', 'sort_order' => 0]);

        $this->getJson(route('api.services.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'First')
            ->assertJsonPath('data.1.name', 'Second');
    }

    public function test_a_service_can_be_read_with_seo_fallbacks(): void
    {
        $service = Service::factory()->create(['story' => "One.\n\nTwo."]);

        $this->getJson(route('api.services.show', $service->slug))
            ->assertOk()
            ->assertJsonPath('data.story', ['One.', 'Two.'])
            ->assertJsonPath('data.seo.title', $service->heading)
            ->assertJsonPath('data.seo.description', $service->intro)
            ->assertJsonPath('data.seo.keywords', [$service->name])
            ->assertJsonPath('data.updatedAt', $service->updated_at->toDateString());
    }

    public function test_hidden_services_are_not_found(): void
    {
        $service = Service::factory()->hidden()->create();

        $this->getJson(route('api.services.show', $service->slug))->assertNotFound();
    }
}
