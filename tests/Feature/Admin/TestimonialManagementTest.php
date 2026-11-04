<?php

namespace Tests\Feature\Admin;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TestimonialManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Http::fake();
        config(['blog.frontend_url' => 'http://frontend.test', 'blog.revalidate_secret' => 'secret']);
    }

    public function test_guests_cannot_manage_testimonials(): void
    {
        $this->get(route('testimonials.index'))->assertRedirect(route('login'));
    }

    public function test_the_list_and_forms_render(): void
    {
        $testimonial = Testimonial::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('testimonials.index'))->assertOk()->assertSee($testimonial->name);
        $this->actingAs($user)->get(route('testimonials.create'))->assertOk();
        $this->actingAs($user)->get(route('testimonials.edit', $testimonial))->assertOk();
    }

    public function test_a_testimonial_can_be_added_with_a_photo_and_rating(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('testimonials.store'), [
                'quote' => 'Brilliant work.',
                'name' => 'Sarah Thompson',
                'role' => 'Owner, Thompson Salon',
                'rating' => '5',
                'photo' => UploadedFile::fake()->image('sarah.jpg', 200, 200),
                'is_active' => '1',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('testimonials.index'))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Testimonial added.']);

        $testimonial = Testimonial::sole();
        $this->assertSame(5, $testimonial->rating);
        Storage::disk('public')->assertExists($testimonial->photo_path);
        Http::assertSent(fn ($request) => $request['tag'] === 'testimonials');
    }

    public function test_the_rating_is_optional_but_must_be_one_to_five(): void
    {
        $data = ['quote' => 'Great.', 'name' => 'A', 'role' => 'B', 'sort_order' => 1];

        $this->actingAs(User::factory()->create())->post(route('testimonials.store'), [...$data, 'rating' => ''])->assertSessionHasNoErrors();
        $this->assertNull(Testimonial::sole()->rating);

        $this->actingAs(User::factory()->create())->post(route('testimonials.store'), [...$data, 'rating' => '9'])->assertSessionHasErrors('rating');
    }

    public function test_a_testimonial_can_be_deleted_with_its_photo(): void
    {
        Storage::disk('public')->put('testimonials/a.jpg', 'x');
        $testimonial = Testimonial::factory()->create(['photo_path' => 'testimonials/a.jpg']);

        $this->actingAs(User::factory()->create())->delete(route('testimonials.destroy', $testimonial))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Testimonial deleted.']);

        $this->assertModelMissing($testimonial);
        Storage::disk('public')->assertMissing('testimonials/a.jpg');
    }
}
