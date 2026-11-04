<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * The inbox, newest first, optionally only unread messages.
     */
    public function index(Request $request): View
    {
        $onlyUnread = $request->boolean('unread');

        return view('messages.index', [
            'messages' => Message::query()
                ->when($onlyUnread, fn ($query) => $query->unread())
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'onlyUnread' => $onlyUnread,
            'unreadCount' => Message::query()->unread()->count(),
        ]);
    }

    /**
     * Read a message. Opening it marks it as read.
     */
    public function show(Message $message): View
    {
        if ($message->isUnread()) {
            $message->update(['read_at' => now()]);
        }

        return view('messages.show', ['message' => $message]);
    }

    /**
     * Mark a message as unread again.
     */
    public function markUnread(Message $message): RedirectResponse
    {
        $message->update(['read_at' => null]);

        return redirect()->route('messages.index')->with('toast', [
            'type' => 'success',
            'message' => __('Marked as unread.'),
        ]);
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('messages.index')->with('toast', [
            'type' => 'success',
            'message' => __('Message deleted.'),
        ]);
    }
}
