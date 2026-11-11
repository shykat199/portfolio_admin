<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_shows_only_the_first_five_active_projects_in_display_order(): void
    {
        Project::factory()->count(7)->sequence(
            ['title' => 'Seventh', 'sort_order' => 7],
            ['title' => 'Third', 'sort_order' => 3],
            ['title' => 'First', 'sort_order' => 1],
            ['title' => 'Sixth', 'sort_order' => 6],
            ['title' => 'Second', 'sort_order' => 2],
            ['title' => 'Fourth', 'sort_order' => 4],
            ['title' => 'Fifth', 'sort_order' => 5],
        )->create();
        Project::factory()->hidden()->create(['sort_order' => 0]);

        $this->getJson(route('api.projects.home'))
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.*.title', ['First', 'Second', 'Third', 'Fourth', 'Fifth']);
    }

    public function test_the_home_page_returns_an_empty_list_when_no_projects_are_active(): void
    {
        Project::factory()->hidden()->create();

        $this->getJson(route('api.projects.home'))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_the_projects_page_includes_projects_beyond_the_home_page_limit(): void
    {
        Project::factory()->count(6)->sequence(
            ['title' => 'Sixth', 'sort_order' => 6],
            ['title' => 'Fifth', 'sort_order' => 5],
            ['title' => 'Fourth', 'sort_order' => 4],
            ['title' => 'Third', 'sort_order' => 3],
            ['title' => 'Second', 'sort_order' => 2],
            ['title' => 'First', 'sort_order' => 1],
        )->create();

        $this->getJson(route('api.projects.index'))
            ->assertOk()
            ->assertJsonCount(6, 'data')
            ->assertJsonPath('data.*.title', ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth']);
    }

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
            'live_url' => 'https://example.com',
            'screenshots' => [['path' => 'work/a.png', 'alt' => 'A', 'caption' => 'Cap']],
        ]);

        $this->getJson(route('api.projects.show', $project->slug))
            ->assertOk()
            ->assertJsonPath('data.cover.src', url('storage/work/cover.webp'))
            ->assertJsonPath('data.liveUrl', 'https://example.com')
            ->assertJsonPath('data.screenshots.0', ['src' => url('storage/work/a.png'), 'alt' => 'A', 'caption' => 'Cap'])
            ->assertJsonPath('data.seo.title', $project->title.' (demo)');
    }

    public function test_hidden_projects_are_not_found(): void
    {
        $project = Project::factory()->hidden()->create();

        $this->getJson(route('api.projects.show', $project->slug))->assertNotFound();
    }
}
