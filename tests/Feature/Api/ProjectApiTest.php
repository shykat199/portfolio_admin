<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_list_shows_active_projects_in_display_order(): void
    {
        Project::factory()->create(['title' => 'Second', 'sort_order' => 2]);
        Project::factory()->create(['title' => 'First', 'sort_order' => 1]);
        Project::factory()->hidden()->create(['sort_order' => 0]);

        $this->getJson(route('api.projects.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title', 'First');
    }

    public function test_a_project_includes_image_urls_and_seo(): void
    {
        $project = Project::factory()->create([
            'is_demo' => true,
            'screenshots' => [['path' => 'work/a.png', 'alt' => 'A', 'caption' => 'Cap']],
        ]);

        $this->getJson(route('api.projects.show', $project->slug))
            ->assertOk()
            ->assertJsonPath('data.cover.src', url('storage/work/cover.webp'))
            ->assertJsonPath('data.screenshots.0', ['src' => url('storage/work/a.png'), 'alt' => 'A', 'caption' => 'Cap'])
            ->assertJsonPath('data.seo.title', $project->title.' (demo)');
    }

    public function test_hidden_projects_are_not_found(): void
    {
        $project = Project::factory()->hidden()->create();

        $this->getJson(route('api.projects.show', $project->slug))->assertNotFound();
    }
}
