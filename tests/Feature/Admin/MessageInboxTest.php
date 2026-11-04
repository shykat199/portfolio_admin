<?php

namespace Tests\Feature\Admin;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_read_messages(): void
    {
        $message = Message::factory()->create();

        $this->get(route('messages.index'))->assertRedirect(route('login'));
        $this->get(route('messages.show', $message))->assertRedirect(route('login'));
    }

    public function test_the_inbox_lists_messages_and_can_filter_unread(): void
    {
        $unread = Message::factory()->create(['name' => 'Unread Person']);
        $read = Message::factory()->read()->create(['name' => 'Read Person']);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('messages.index'))->assertOk()->assertSee('Unread Person')->assertSee('Read Person');
        $this->actingAs($user)->get(route('messages.index', ['unread' => 1]))->assertOk()->assertSee('Unread Person')->assertDontSee('Read Person');
    }

    public function test_opening_a_message_marks_it_read_and_it_can_be_marked_unread(): void
    {
        $message = Message::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('messages.show', $message))->assertOk()->assertSee($message->email);
        $this->assertFalse($message->fresh()->isUnread());

        $this->actingAs($user)->patch(route('messages.unread', $message))
            ->assertRedirect(route('messages.index'))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Marked as unread.']);
        $this->assertTrue($message->fresh()->isUnread());
    }

    public function test_message_text_is_escaped(): void
    {
        $message = Message::factory()->create(['message' => '<script>alert(1)</script>']);

        $this->actingAs(User::factory()->create())->get(route('messages.show', $message))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_a_message_can_be_deleted(): void
    {
        $message = Message::factory()->create();

        $this->actingAs(User::factory()->create())->delete(route('messages.destroy', $message))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Message deleted.']);
        $this->assertModelMissing($message);
    }
}
