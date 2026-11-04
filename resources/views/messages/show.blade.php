<x-layouts.app :title="__('Message from :name', ['name' => $message->name])">
    <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted hover:text-fg">← {{ __('Back to messages') }}</a>

    <div class="mt-4 grid gap-6 xl:grid-cols-3">
        <article class="rounded-2xl border border-line bg-raised p-5 sm:p-7 xl:col-span-2">
            <p class="font-mono text-xs text-muted">{{ $message->created_at->format('l j F Y, H:i') }}</p>
            <h2 class="mt-2 text-xl font-semibold">{{ $message->name }} <span class="font-normal text-muted">· {{ $message->business }}</span></h2>
            <div class="mt-6 text-[0.975rem] leading-relaxed whitespace-pre-line">{{ $message->message }}</div>
        </article>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-muted">{{ __('Email') }}</dt>
                        <dd class="mt-0.5 break-all"><a href="mailto:{{ $message->email }}" class="text-accent hover:underline">{{ $message->email }}</a></dd>
                    </div>
                    <div>
                        <dt class="text-muted">{{ __('Business') }}</dt>
                        <dd class="mt-0.5">{{ $message->business }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted">{{ __('Website') }}</dt>
                        <dd class="mt-0.5 break-all">
                            @if ($message->websiteUrl())
                                <a href="{{ $message->websiteUrl() }}" target="_blank" rel="noopener noreferrer nofollow" class="text-accent hover:underline">{{ $message->website }}</a>
                            @else
                                <span class="text-muted">{{ __('Not given') }}</span>
                            @endif
                        </dd>
                    </div>
                    @if ($message->ip_address)
                        <div>
                            <dt class="text-muted">{{ __('Sent from IP') }}</dt>
                            <dd class="mt-0.5 font-mono">{{ $message->ip_address }}</dd>
                        </div>
                    @endif
                </dl>

                <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: your enquiry') }}" class="mt-6 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 14 4 9l5-5M4 9h11a5 5 0 0 1 5 5v6"/></svg>
                    {{ __('Reply by email') }}
                </a>
            </section>

            <div class="flex gap-2">
                <form method="POST" action="{{ route('messages.unread', $message) }}" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex min-h-10 w-full cursor-pointer items-center justify-center rounded-lg border border-line px-3 text-sm text-muted hover:text-fg">{{ __('Mark as unread') }}</button>
                </form>
                <form method="POST" action="{{ route('messages.destroy', $message) }}" class="flex-1" data-confirm="{{ __('Delete this message? This cannot be undone.') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex min-h-10 w-full cursor-pointer items-center justify-center rounded-lg border border-line px-3 text-sm text-muted hover:border-error/50 hover:text-error">{{ __('Delete') }}</button>
                </form>
            </div>
        </aside>
    </div>
</x-layouts.app>
