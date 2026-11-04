<x-layouts.app :title="__('Services')">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-muted">{{ trans_choice(':count service|:count services', $services->count()) }} · {{ __('shown on the website in this order') }}</p>
        <a href="{{ route('services.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('New service') }}
        </a>
    </div>

    @if ($services->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-line p-10 text-center">
            <p class="font-medium">{{ __('No services yet') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('Add your first service package to show it on your website.') }}</p>
        </div>
    @else
        <ul class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($services as $service)
                <li class="flex flex-col rounded-2xl border border-line bg-raised p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-mono text-xs text-muted">#{{ $service->sort_order }}</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs {{ $service->is_active ? 'border-accent/40 bg-accent/10 text-accent' : 'border-line text-muted' }}">
                            <span class="size-1.5 rounded-full {{ $service->is_active ? 'bg-accent' : 'bg-muted' }}"></span>
                            {{ $service->is_active ? __('Shown') : __('Hidden') }}
                        </span>
                    </div>
                    <a href="{{ route('services.edit', $service) }}" class="mt-3 text-xl font-semibold leading-snug hover:text-accent">{{ $service->name }}</a>
                    <p class="mt-1.5 line-clamp-2 text-sm text-muted">{{ $service->for_who }}</p>
                    <p class="mt-4 text-2xl font-semibold text-accent">£{{ number_format($service->price) }}<span class="text-sm font-normal text-muted">{{ $service->price_suffix }}</span></p>
                    <p class="text-xs text-muted">{{ $service->time_label }}: {{ $service->time }}</p>

                    <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-line pt-4">
                        @if ($service->is_active && config('blog.frontend_url'))
                            <a href="{{ rtrim(config('blog.frontend_url'), '/') }}/services/{{ $service->slug }}" target="_blank" rel="noopener" class="inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm text-muted hover:border-fg/30 hover:text-fg">{{ __('View') }}</a>
                        @endif
                        <a href="{{ route('services.edit', $service) }}" class="inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm hover:border-accent hover:text-accent">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('services.destroy', $service) }}" class="ml-auto" data-confirm="{{ __('Delete “:name”? Blog posts linked to it will be unlinked. This cannot be undone.', ['name' => $service->name]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg border border-line px-3 text-sm text-muted hover:border-error/50 hover:text-error">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.app>
