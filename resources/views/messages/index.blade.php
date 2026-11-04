<x-layouts.app :title="__('Messages')">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-muted">{{ trans_choice(':count unread message|:count unread messages', $unreadCount) }}</p>
        <div class="flex rounded-lg border border-line p-0.5 text-sm">
            <a href="{{ route('messages.index') }}" class="rounded-md px-3 py-1.5 {{ $onlyUnread ? 'text-muted hover:text-fg' : 'bg-fg/10 text-fg' }}">{{ __('All') }}</a>
            <a href="{{ route('messages.index', ['unread' => 1]) }}" class="rounded-md px-3 py-1.5 {{ $onlyUnread ? 'bg-fg/10 text-fg' : 'text-muted hover:text-fg' }}">{{ __('Unread') }}</a>
        </div>
    </div>

    @if ($messages->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-line p-10 text-center">
            <p class="font-medium">{{ $onlyUnread ? __('All caught up') : __('No messages yet') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('Enquiries sent from your website’s contact form appear here.') }}</p>
        </div>
    @else
        <ul class="mt-6 divide-y divide-line overflow-hidden rounded-2xl border border-line bg-raised">
            @foreach ($messages as $message)
                <li>
                    <a href="{{ route('messages.show', $message) }}" class="flex gap-4 p-4 transition-colors hover:bg-fg/[0.03] sm:p-5">
                        <span class="mt-2 size-2 shrink-0 rounded-full {{ $message->isUnread() ? 'bg-accent' : 'bg-transparent' }}" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                <p class="truncate {{ $message->isUnread() ? 'font-semibold' : 'text-fg/80' }}">
                                    {{ $message->name }} <span class="font-normal text-muted">· {{ $message->business }}</span>
                                    @if ($message->isUnread())<span class="sr-only">({{ __('unread') }})</span>@endif
                                </p>
                                <time datetime="{{ $message->created_at->toIso8601String() }}" class="shrink-0 font-mono text-xs text-muted">{{ $message->created_at->diffForHumans() }}</time>
                            </div>
                            <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $message->message }}</p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="mt-6">{{ $messages->links() }}</div>
    @endif
</x-layouts.app>
