<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use App\PostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Http::fake();
        config(['blog.frontend_url' => 'http://frontend.test', 'blog.revalidate_secret' => 'secret']);
        Service::factory()->create(['slug' => 'small-business-website-design']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return [
            'title' => 'How to plan a website',
            'slug' => '',
            'description' => 'A short summary of the article for the blog list and Google.',
            'category' => 'Planning',
            'tags' => 'Websites, Planning, , Websites',
            'body' => "## First section\n\nSome text.",
            'faq' => [
                ['question' => 'Is it hard?', 'answer' => 'No.'],
                ['question' => '', 'answer' => ''],
            ],
            'service' => 'small-business-website-design',
            'status' => 'published',
            'seo_title' => 'Plan a Website in 5 Steps',
            'meta_description' => 'A step-by-step guide to planning a small business website.',
            'meta_keywords' => 'website planning, small business website, Website Planning',
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_posts(): void
    {
        $this->get(route('posts.index'))->assertRedirect(route('login'));
        $this->post(route('posts.store'), $this->validData())->assertRedirect(route('login'));
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_the_posts_list_and_forms_render(): void
    {
        $post = Post::factory()->published()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('posts.index'))->assertOk()->assertSee($post->title);
        $this->actingAs($user)->get(route('posts.create'))->assertOk();
        $this->actingAs($user)->get(route('posts.edit', $post))->assertOk()->assertSee($post->title);
    }

    public function test_a_post_can_be_published_with_a_cover_and_the_website_is_refreshed(): void
    {
        $response = $this->actingAs(User::factory()->create())->post(route('posts.store'), $this->validData([
            'cover' => UploadedFile::fake()->image('cover.jpg', 1200, 630),
            'cover_alt' => 'A laptop on a desk',
        ]));

        $post = Post::sole();
        $response->assertRedirect(route('posts.edit', $post))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Post published.']);

        $this->assertSame('how-to-plan-a-website', $post->slug);
        $this->assertSame(['Websites', 'Planning'], $post->tags);
        $this->assertSame(['website planning', 'small business website'], $post->meta_keywords);
        $this->assertSame('Plan a Website in 5 Steps', $post->seo()['title']);
        $this->assertCount(1, $post->faq);
        $this->assertTrue($post->isLive());
        Storage::disk('public')->assertExists($post->cover_path);

        Http::assertSent(fn ($request) => $request->url() === 'http://frontend.test/api/revalidate'
            && $request->hasHeader('x-revalidate-secret', 'secret'));
    }

    public function test_a_post_can_be_saved_as_a_draft(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('posts.store'), $this->validData(['status' => 'draft']))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Draft saved.']);

        $post = Post::sole();
        $this->assertSame(PostStatus::Draft, $post->status);
        $this->assertNull($post->published_at);
    }

    public function test_invalid_posts_are_rejected(): void
    {
        $existing = Post::factory()->create(['slug' => 'taken']);

        $this->actingAs(User::factory()->create())
            ->post(route('posts.store'), $this->validData([
                'title' => '',
                'slug' => 'taken',
                'cover' => UploadedFile::fake()->create('cover.svg', 10, 'image/svg+xml'),
            ]))
            ->assertSessionHasErrors(['title', 'slug', 'cover']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertTrue($existing->is(Post::sole()));
    }

    public function test_a_post_can_be_updated_and_its_old_cover_replaced(): void
    {
        $post = Post::factory()->published()->create(['cover_path' => 'blog/old.jpg']);
        Storage::disk('public')->put('blog/old.jpg', 'old');

        $this->actingAs(User::factory()->create())
            ->put(route('posts.update', $post), $this->validData([
                'title' => 'A new title',
                'slug' => $post->slug,
                'cover' => UploadedFile::fake()->image('new.png', 1200, 630),
                'cover_alt' => 'New cover',
            ]))
            ->assertRedirect(route('posts.edit', $post))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Changes saved.']);

        $post->refresh();
        $this->assertSame('A new title', $post->title);
        Storage::disk('public')->assertMissing('blog/old.jpg');
        Storage::disk('public')->assertExists($post->cover_path);
    }

    public function test_a_post_can_be_deleted(): void
    {
        $post = Post::factory()->create(['cover_path' => 'blog/cover.jpg']);
        Storage::disk('public')->put('blog/cover.jpg', 'image');

        $this->actingAs(User::factory()->create())
            ->delete(route('posts.destroy', $post))
            ->assertRedirect(route('posts.index'))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Post deleted.']);

        $this->assertModelMissing($post);
        Storage::disk('public')->assertMissing('blog/cover.jpg');
    }
}
