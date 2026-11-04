<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_use_the_editor_endpoints(): void
    {
        $this->postJson(route('posts.preview'), ['body' => '# Hi'])->assertUnauthorized();
        $this->postJson(route('posts.images.store'))->assertUnauthorized();
    }

    public function test_the_preview_renders_markdown_safely(): void
    {
        $response = $this->actingAs(User::factory()->create())->postJson(route('posts.preview'), [
            'body' => "# Title\n\n**bold** _italic_ ~~gone~~ `code`\n\n<script>alert(1)</script>\n\n[bad](javascript:alert(1))",
        ]);

        $html = $response->assertOk()->json('html');
        $this->assertStringContainsString('<h2 data-level="1" id="title">Title</h2>', $html);
        $this->assertStringContainsString('<strong>bold</strong> <em>italic</em> <del>gone</del> <code>code</code>', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_images_can_be_uploaded_for_articles(): void
    {
        Storage::fake('public');

        $response = $this->actingAs(User::factory()->create())->postJson(route('posts.images.store'), [
            'image' => UploadedFile::fake()->image('diagram.png', 800, 600),
        ]);

        $response->assertCreated();
        $path = str($response->json('url'))->after('/storage/')->toString();
        Storage::disk('public')->assertExists($path);
    }

    public function test_only_images_can_be_uploaded(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('posts.images.store'), ['image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')])
            ->assertUnprocessable();
    }

    public function test_the_api_returns_the_article_as_html_with_its_contents(): void
    {
        $post = Post::factory()->published()->create(['body' => "## First part\n\nText.\n\n## Second part\n\nMore."]);

        $this->getJson(route('api.posts.show', $post->slug))
            ->assertOk()
            ->assertJsonPath('data.toc.0', ['id' => 'first-part', 'text' => 'First part'])
            ->assertJsonPath('data.toc.1.id', 'second-part')
            ->assertJsonMissingPath('data.body');
    }
}
