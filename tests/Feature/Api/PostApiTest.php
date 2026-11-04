<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_list_only_includes_published_posts_without_their_body(): void
    {
        $published = Post::factory()->published()->create();
        Post::factory()->create();
        Post::factory()->published()->create(['published_at' => now()->addWeek()]);

        $this->getJson(route('api.posts.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', $published->slug)
            ->assertJsonMissingPath('data.0.body');
    }

    public function test_a_published_post_can_be_read_in_full(): void
    {
        $post = Post::factory()->published()->create();

        $this->getJson(route('api.posts.show', $post->slug))
            ->assertOk()
            ->assertJsonPath('data.title', $post->title)
            ->assertJsonPath('data.toc', [['id' => 'introduction', 'text' => 'Introduction']])
            ->assertJsonPath('data.faq.0.question', $post->faq[0]['question']);
    }

    public function test_seo_fields_fall_back_to_the_title_summary_and_topics(): void
    {
        $plain = Post::factory()->published()->create(['tags' => ['Pricing']]);
        $custom = Post::factory()->published()->create([
            'seo_title' => 'Custom title',
            'meta_description' => 'Custom description',
            'meta_keywords' => ['custom keyword'],
        ]);

        $this->getJson(route('api.posts.show', $plain->slug))
            ->assertJsonPath('data.seo', ['title' => $plain->title, 'description' => $plain->description, 'keywords' => ['Pricing']]);

        $this->getJson(route('api.posts.show', $custom->slug))
            ->assertJsonPath('data.seo', ['title' => 'Custom title', 'description' => 'Custom description', 'keywords' => ['custom keyword']]);
    }

    public function test_drafts_are_not_found(): void
    {
        $draft = Post::factory()->create();

        $this->getJson(route('api.posts.show', $draft->slug))->assertNotFound();
    }
}
