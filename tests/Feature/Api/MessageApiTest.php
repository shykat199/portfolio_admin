<?php

namespace Tests\Feature\Api;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['blog.revalidate_secret' => 'site-secret']);
    }

    /**
     * @return array<string, string>
     */
    private function validData(): array
    {
        return [
            'name' => 'Sarah Thompson',
            'email' => 'sarah@example.com',
            'business' => 'Thompson Salon',
            'website' => 'thompsonsalon.com',
            'message' => 'I would like an online booking website for my salon.',
            'ip_address' => '203.0.113.7',
            'user_agent' => 'Mozilla/5.0',
        ];
    }

    public function test_the_website_can_post_a_message(): void
    {
        $this->postJson(route('api.messages.store'), $this->validData(), ['x-site-secret' => 'site-secret'])
            ->assertCreated();

        $message = Message::sole();
        $this->assertSame('Sarah Thompson', $message->name);
        $this->assertSame('203.0.113.7', $message->ip_address);
        $this->assertTrue($message->isUnread());
    }

    public function test_requests_without_the_site_secret_are_refused(): void
    {
        $this->postJson(route('api.messages.store'), $this->validData())->assertUnauthorized();
        $this->postJson(route('api.messages.store'), $this->validData(), ['x-site-secret' => 'wrong'])->assertUnauthorized();

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_invalid_messages_are_rejected(): void
    {
        $this->postJson(route('api.messages.store'), [...$this->validData(), 'email' => 'not-an-email', 'message' => 'Hi'], ['x-site-secret' => 'site-secret'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'message']);
    }
}
