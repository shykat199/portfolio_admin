<?php

namespace Tests\Feature\Admin;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FaqManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
        config(['blog.frontend_url' => 'http://frontend.test', 'blog.revalidate_secret' => 'secret']);
    }

    public function test_guests_cannot_manage_the_faq(): void
    {
        $this->get(route('faqs.index'))->assertRedirect(route('login'));
    }

    public function test_the_list_and_forms_render(): void
    {
        $faq = Faq::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('faqs.index'))->assertOk()->assertSee($faq->question);
        $this->actingAs($user)->get(route('faqs.create'))->assertOk();
        $this->actingAs($user)->get(route('faqs.edit', $faq))->assertOk();
    }

    public function test_questions_can_be_added_updated_and_deleted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('faqs.store'), ['question' => 'Do you build apps?', 'answer' => 'Yes.', 'is_active' => '1', 'sort_order' => 1])
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Question added.']);
        $faq = Faq::sole();
        Http::assertSent(fn ($request) => $request['tag'] === 'faqs');

        $this->actingAs($user)
            ->put(route('faqs.update', $faq), ['question' => 'Do you build mobile apps?', 'answer' => 'Yes.', 'is_active' => '0', 'sort_order' => 2])
            ->assertSessionHasNoErrors();
        $this->assertFalse($faq->fresh()->is_active);

        $this->actingAs($user)->delete(route('faqs.destroy', $faq))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Question deleted.']);
        $this->assertModelMissing($faq);
    }

    public function test_questions_and_answers_are_required(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('faqs.store'), ['question' => '', 'answer' => '', 'sort_order' => 1])
            ->assertSessionHasErrors(['question', 'answer']);
    }
}
