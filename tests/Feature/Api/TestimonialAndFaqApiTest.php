<?php

namespace Tests\Feature\Api;

use App\Models\Faq;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialAndFaqApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_testimonials_list_shown_items_in_order(): void
    {
        Testimonial::factory()->create(['name' => 'Second', 'sort_order' => 2]);
        Testimonial::factory()->create(['name' => 'First', 'sort_order' => 1, 'rating' => 5]);
        Testimonial::factory()->hidden()->create(['sort_order' => 0]);

        $this->getJson(route('api.testimonials.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'First')
            ->assertJsonPath('data.0.rating', 5)
            ->assertJsonPath('data.0.photo', null);
    }

    public function test_faqs_list_shown_questions_in_order(): void
    {
        Faq::factory()->create(['question' => 'Second?', 'sort_order' => 2]);
        Faq::factory()->create(['question' => 'First?', 'sort_order' => 1]);
        Faq::factory()->hidden()->create(['sort_order' => 0]);

        $this->getJson(route('api.faqs.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.question', 'First?');
    }
}
