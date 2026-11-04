<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
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
            'name' => 'Landing page',
            'slug' => '',
            'for_who' => 'For launches that need one great page.',
            'includes' => "One long page\n- Contact form\n\nFast on phones",
            'price' => 395,
            'price_suffix' => '',
            'billed_monthly' => '0',
            'time_label' => 'Delivery',
            'time' => '1 week',
            'heading' => 'Landing pages that convert',
            'intro' => 'A single focused page built to turn visitors into enquiries.',
            'story' => "First paragraph.\n\nSecond paragraph.",
            'ideal_for' => "Product launches\nEvents",
            'faq' => [['question' => 'How fast?', 'answer' => 'One week.'], ['question' => '', 'answer' => '']],
            'related_projects' => ['clinic-website'],
            'meta_keywords' => 'landing page, landing page design',
            'is_active' => '1',
            'sort_order' => 4,
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_services(): void
    {
        $this->get(route('services.index'))->assertRedirect(route('login'));
        $this->post(route('services.store'), $this->validData())->assertRedirect(route('login'));
    }

    public function test_the_services_list_and_forms_render(): void
    {
        $service = Service::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('services.index'))->assertOk()->assertSee($service->name);
        $this->actingAs($user)->get(route('services.create'))->assertOk();
        $this->actingAs($user)->get(route('services.edit', $service))->assertOk()->assertSee($service->name);
    }

    public function test_a_service_can_be_created_and_the_website_is_refreshed(): void
    {
        Project::factory()->create(['slug' => 'clinic-website']);

        $this->actingAs(User::factory()->create())
            ->post(route('services.store'), $this->validData())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Service created and shown on the website.']);

        $service = Service::sole();
        $this->assertSame('landing-page', $service->slug);
        $this->assertSame(['One long page', 'Contact form', 'Fast on phones'], $service->includes);
        $this->assertSame(['clinic-website'], $service->related_projects);
        $this->assertSame(['First paragraph.', 'Second paragraph.'], $service->storyParagraphs());
        $this->assertCount(1, $service->faq);
        $this->assertTrue($service->is_active);

        Http::assertSent(fn ($request) => $request['tag'] === 'services');
    }

    public function test_invalid_services_are_rejected(): void
    {
        Service::factory()->create(['slug' => 'taken']);

        $this->actingAs(User::factory()->create())
            ->post(route('services.store'), $this->validData(['name' => '', 'slug' => 'taken', 'price' => -5, 'includes' => '']))
            ->assertSessionHasErrors(['name', 'slug', 'price', 'includes']);

        $this->assertDatabaseCount('services', 1);
    }

    public function test_renaming_a_service_address_keeps_blog_posts_linked(): void
    {
        Project::factory()->create(['slug' => 'clinic-website']);
        $service = Service::factory()->create(['slug' => 'old-address']);
        $post = Post::factory()->create(['service' => 'old-address']);

        $this->actingAs(User::factory()->create())
            ->put(route('services.update', $service), $this->validData(['slug' => 'new-address']))
            ->assertRedirect(route('services.edit', $service));

        $this->assertSame('new-address', $post->fresh()->service);
    }

    public function test_deleting_a_service_unlinks_its_blog_posts(): void
    {
        $service = Service::factory()->create();
        $post = Post::factory()->create(['service' => $service->slug]);

        $this->actingAs(User::factory()->create())
            ->delete(route('services.destroy', $service))
            ->assertRedirect(route('services.index'))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Service deleted.']);

        $this->assertModelMissing($service);
        $this->assertNull($post->fresh()->service);
    }
}
