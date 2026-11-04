<?php

namespace Tests\Feature\Admin;

use App\Models\TechItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TechItemManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Http::fake();
        config(['blog.frontend_url' => 'http://frontend.test', 'blog.revalidate_secret' => 'secret']);
    }

    public function test_guests_cannot_manage_the_tech_stack(): void
    {
        $this->get(route('tech.index'))->assertRedirect(route('login'));
        $this->post(route('tech.store'), ['name' => 'Vue'])->assertRedirect(route('login'));
    }

    public function test_the_list_and_forms_render(): void
    {
        $item = TechItem::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('tech.index'))->assertOk()->assertSee($item->name);
        $this->actingAs($user)->get(route('tech.create'))->assertOk();
        $this->actingAs($user)->get(route('tech.edit', $item))->assertOk();
    }

    public function test_a_technology_can_be_added_with_a_logo(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('tech.store'), [
                'name' => '  Vue  ',
                'logo' => UploadedFile::fake()->image('vue.png', 128, 128),
                'is_active' => '1',
                'sort_order' => 3,
            ])
            ->assertRedirect(route('tech.index'))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Vue added to your tech stack.']);

        $item = TechItem::sole();
        $this->assertSame('Vue', $item->name);
        Storage::disk('public')->assertExists($item->logo_path);
        Http::assertSent(fn ($request) => $request['tag'] === 'tech');
    }

    public function test_names_must_be_unique_and_logos_must_be_images(): void
    {
        TechItem::factory()->create(['name' => 'React']);

        $this->actingAs(User::factory()->create())
            ->post(route('tech.store'), [
                'name' => 'React',
                'logo' => UploadedFile::fake()->create('logo.pdf', 10, 'application/pdf'),
                'sort_order' => 1,
            ])
            ->assertSessionHasErrors(['name', 'logo']);
    }

    public function test_a_logo_can_be_removed_and_an_item_deleted(): void
    {
        Storage::disk('public')->put('tech/old.png', 'x');
        $item = TechItem::factory()->create(['name' => 'Docker', 'logo_path' => 'tech/old.png']);

        $this->actingAs(User::factory()->create())
            ->put(route('tech.update', $item), ['name' => 'Docker', 'remove_logo' => '1', 'is_active' => '1', 'sort_order' => 1])
            ->assertSessionHasNoErrors();

        $this->assertNull($item->fresh()->logo_path);
        Storage::disk('public')->assertMissing('tech/old.png');

        $this->actingAs(User::factory()->create())
            ->delete(route('tech.destroy', $item))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Docker removed.']);
        $this->assertModelMissing($item);
    }
}
