<?php

namespace Tests\Feature\Admin;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExperienceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
        config(['blog.frontend_url' => 'http://frontend.test', 'blog.revalidate_secret' => 'secret']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return [
            'role' => 'Lead Developer',
            'company' => 'Acme',
            'company_url' => 'https://acme.test',
            'type' => 'Full-time',
            'location' => 'Remote',
            'start' => 'Jan 2026',
            'end' => 'Dec 2026',
            'is_current' => '1',
            'summary' => 'Leading the web team.',
            'highlights' => "Shipped the new site\n\n- Mentored two developers",
            'tech' => 'Laravel, React, laravel',
            'is_active' => '1',
            'sort_order' => 1,
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_experience(): void
    {
        $this->get(route('experience.index'))->assertRedirect(route('login'));
        $this->post(route('experience.store'), $this->validData())->assertRedirect(route('login'));
    }

    public function test_the_list_and_forms_render(): void
    {
        $job = Experience::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('experience.index'))->assertOk()->assertSee($job->company);
        $this->actingAs($user)->get(route('experience.create'))->assertOk();
        $this->actingAs($user)->get(route('experience.edit', $job))->assertOk();
    }

    public function test_a_current_job_can_be_added(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('experience.store'), $this->validData())
            ->assertRedirect(route('experience.index'))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Lead Developer at Acme added.']);

        $job = Experience::sole();
        $this->assertNull($job->end);
        $this->assertTrue($job->isCurrent());
        $this->assertSame(['Shipped the new site', 'Mentored two developers'], $job->highlights);
        $this->assertSame(['Laravel', 'React'], $job->tech);
        Http::assertSent(fn ($request) => $request['tag'] === 'experience');
    }

    public function test_invalid_jobs_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('experience.store'), $this->validData(['role' => '', 'company_url' => 'javascript:alert(1)']))
            ->assertSessionHasErrors(['role', 'company_url']);
    }

    public function test_a_job_can_be_updated_and_deleted(): void
    {
        $job = Experience::factory()->create();

        $this->actingAs(User::factory()->create())
            ->put(route('experience.update', $job), $this->validData(['is_current' => '0', 'end' => 'Mar 2026']))
            ->assertSessionHasNoErrors();
        $this->assertSame('Mar 2026', $job->fresh()->end);

        $this->actingAs(User::factory()->create())
            ->delete(route('experience.destroy', $job))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Job removed.']);
        $this->assertModelMissing($job);
    }
}
