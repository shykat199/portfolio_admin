<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Http::fake();
        config(['blog.frontend_url' => 'http://frontend.test', 'blog.revalidate_secret' => 'secret']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return [
            'title' => 'Bakery ordering website',
            'slug' => '',
            'is_demo' => '0',
            'category' => 'Bakery',
            'year' => '2026',
            'service' => '',
            'problem' => 'Orders came in by phone.',
            'solution' => 'Online ordering.',
            'result' => 'Fewer calls.',
            'overview' => 'A website for a local bakery.',
            'client_problem' => 'Phone orders took hours every day.',
            'built' => "Online menu\n- Order form",
            'tech' => 'Laravel, React, laravel',
            'results' => 'Fewer phone calls',
            'cover' => UploadedFile::fake()->image('cover.jpg', 1600, 1000),
            'cover_alt' => 'The bakery website',
            'is_active' => '1',
            'sort_order' => 1,
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_projects(): void
    {
        $this->get(route('projects.index'))->assertRedirect(route('login'));
        $this->post(route('projects.store'), $this->validData())->assertRedirect(route('login'));
    }

    public function test_the_projects_list_and_forms_render(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('projects.index'))->assertOk()->assertSee($project->title);
        $this->actingAs($user)->get(route('projects.create'))->assertOk();
        $this->actingAs($user)->get(route('projects.edit', $project))->assertOk()->assertSee($project->title);
    }

    public function test_a_project_can_be_created_with_screenshots(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('projects.store'), $this->validData([
                'live_url' => 'https://example.com',
                'screenshots' => [
                    ['file' => UploadedFile::fake()->image('one.png', 1200, 800), 'alt' => 'Menu page', 'caption' => 'The menu'],
                ],
            ]))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Project created and shown on the website.']);

        $project = Project::sole();
        $this->assertSame('bakery-ordering-website', $project->slug);
        $this->assertSame('https://example.com', $project->live_url);
        $this->assertSame(['Online menu', 'Order form'], $project->built);
        $this->assertSame(['Laravel', 'React'], $project->tech);
        Storage::disk('public')->assertExists($project->cover_path);
        $this->assertCount(1, $project->screenshots);
        Storage::disk('public')->assertExists($project->screenshots[0]['path']);

        Http::assertSent(fn ($request) => $request['tag'] === 'projects');
    }

    public function test_a_project_can_be_created_without_a_screenshot_description(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('projects.store'), $this->validData([
                'screenshots' => [
                    ['file' => UploadedFile::fake()->image('one.png', 1200, 800), 'alt' => ''],
                ],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('', Project::sole()->screenshots[0]['alt']);
    }

    public function test_an_invalid_live_project_url_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('projects.store'), $this->validData(['live_url' => 'not a URL']))
            ->assertSessionHasErrors('live_url');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_a_cover_image_is_required(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('projects.store'), $this->validData(['cover' => null]))
            ->assertSessionHasErrors('cover');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_screenshots_can_be_kept_removed_and_added(): void
    {
        Storage::disk('public')->put('work/keep.png', 'a');
        Storage::disk('public')->put('work/drop.png', 'b');
        $project = Project::factory()->create([
            'cover_path' => 'work/cover.png',
            'screenshots' => [
                ['path' => 'work/keep.png', 'alt' => 'Keep', 'caption' => ''],
                ['path' => 'work/drop.png', 'alt' => 'Drop', 'caption' => ''],
            ],
        ]);

        $this->actingAs(User::factory()->create())
            ->put(route('projects.update', $project), $this->validData([
                'slug' => $project->slug,
                'cover' => null,
                'screenshots' => [
                    ['path' => 'work/keep.png', 'alt' => 'Kept', 'caption' => 'Still here'],
                    ['path' => 'work/drop.png', 'alt' => 'Drop', 'remove' => '1'],
                    ['file' => UploadedFile::fake()->image('new.png', 1200, 800), 'alt' => 'New one'],
                ],
            ]))
            ->assertSessionHasNoErrors();

        $shots = $project->fresh()->screenshots;
        $this->assertCount(2, $shots);
        $this->assertEquals(['path' => 'work/keep.png', 'alt' => 'Kept', 'caption' => 'Still here'], $shots[0]);
        Storage::disk('public')->assertMissing('work/drop.png');
        Storage::disk('public')->assertExists($shots[1]['path']);
    }

    public function test_screenshot_paths_that_do_not_belong_to_the_project_are_rejected(): void
    {
        $project = Project::factory()->create(['screenshots' => []]);

        $this->actingAs(User::factory()->create())
            ->put(route('projects.update', $project), $this->validData([
                'slug' => $project->slug,
                'cover' => null,
                'screenshots' => [['path' => '../../.env', 'alt' => 'Sneaky']],
            ]))
            ->assertSessionHasErrors('screenshots.0.path');
    }

    public function test_renaming_or_deleting_a_project_updates_services(): void
    {
        $project = Project::factory()->create(['slug' => 'old-project']);
        $service = Service::factory()->create(['related_projects' => ['old-project', 'other']]);

        $this->actingAs(User::factory()->create())
            ->put(route('projects.update', $project), $this->validData([
                'slug' => 'new-project',
                'live_url' => 'https://updated.example.com',
                'cover' => null,
            ]))
            ->assertSessionHasNoErrors();
        $this->assertSame('https://updated.example.com', $project->fresh()->live_url);
        $this->assertSame(['new-project', 'other'], $service->fresh()->related_projects);

        $this->actingAs(User::factory()->create())
            ->delete(route('projects.destroy', $project->fresh()))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Project deleted.']);
        $this->assertSame(['other'], $service->fresh()->related_projects);
        $this->assertModelMissing($project);
    }
}
