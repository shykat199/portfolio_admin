<x-layouts.app :title="__('Tech stack')">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-muted">{{ trans_choice(':count item|:count items', $items->count()) }} · {{ __('spread across the three scrolling rows on the website, in this order') }}</p>
        <a href="{{ route('tech.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('Add technology') }}
        </a>
    </div>

    @if ($items->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-line p-10 text-center">
            <p class="font-medium">{{ __('No technologies yet') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('Add the tools you work with to show them on your website.') }}</p>
        </div>
    @else
        <ul class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
            @foreach ($items as $item)
                <li class="flex items-center gap-3 rounded-xl border border-line bg-raised p-3 {{ $item->is_active ? '' : 'opacity-60' }}">
                    <span class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-full border border-line bg-surface">
                        @if ($item->logo_url)
                            <img src="{{ $item->logo_url }}" alt="" class="size-7 object-contain">
                        @else
                            <span class="font-mono text-xs font-semibold text-accent">{{ Str::substr($item->name, 0, 2) }}</span>
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('tech.edit', $item) }}" class="block truncate font-medium hover:text-accent">{{ $item->name }}</a>
                        <p class="text-xs text-muted">#{{ $item->sort_order }} · {{ $item->is_active ? __('Shown') : __('Hidden') }}{{ $item->logo_url ? '' : ' · '.__('no logo') }}</p>
                    </div>
                    <a href="{{ route('tech.edit', $item) }}" aria-label="{{ __('Edit :name', ['name' => $item->name]) }}" class="flex size-9 items-center justify-center rounded-lg text-muted hover:bg-fg/5 hover:text-accent">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16v4ZM14 6l4 4"/></svg>
                    </a>
                    <form method="POST" action="{{ route('tech.destroy', $item) }}" data-confirm="{{ __('Remove :name from your tech stack?', ['name' => $item->name]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" aria-label="{{ __('Remove :name', ['name' => $item->name]) }}" class="flex size-9 cursor-pointer items-center justify-center rounded-lg text-muted hover:bg-error/10 hover:text-error">
                            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 7h14M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.app>
