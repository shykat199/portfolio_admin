<x-layouts.app :title="__('FAQ')">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-muted">{{ trans_choice(':count question|:count questions', $faqs->count()) }} · {{ __('the FAQ section on the home page, in this order') }}</p>
        <a href="{{ route('faqs.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('Add question') }}
        </a>
    </div>
    <p class="mt-2 text-xs text-muted">{{ __('Service pages and blog posts have their own FAQ in their own forms.') }}</p>

    @if ($faqs->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-line p-10 text-center">
            <p class="font-medium">{{ __('No questions yet') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('The FAQ section stays hidden on the website until you add one.') }}</p>
        </div>
    @else
        <ul class="mt-6 divide-y divide-line overflow-hidden rounded-2xl border border-line bg-raised">
            @foreach ($faqs as $faq)
                <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-start sm:p-5 {{ $faq->is_active ? '' : 'opacity-60' }}">
                    <span class="font-mono text-xs text-accent sm:mt-1 sm:w-8">{{ str_pad((string) $faq->sort_order, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('faqs.edit', $faq) }}" class="font-medium hover:text-accent">{{ $faq->question }}</a>
                        <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $faq->answer }}</p>
                        @unless ($faq->is_active)
                            <p class="mt-1 text-xs text-muted">{{ __('Hidden') }}</p>
                        @endunless
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('faqs.edit', $faq) }}" class="inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm hover:border-accent hover:text-accent">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('faqs.destroy', $faq) }}" data-confirm="{{ __('Delete this question?') }}">
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
