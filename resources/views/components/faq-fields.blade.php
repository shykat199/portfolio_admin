@props(['items' => [], 'hint' => ''])

@php
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
    $rows = $items ?: [['question' => '', 'answer' => '']];
@endphp

<section class="rounded-2xl border border-line bg-raised p-5 sm:p-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="font-medium">{{ __('Frequently asked questions') }}</h2>
            <p class="text-xs text-muted">{{ $hint }}</p>
        </div>
        <button type="button" data-faq-add class="inline-flex min-h-9 shrink-0 cursor-pointer items-center gap-1.5 rounded-lg border border-line px-3 text-sm hover:border-accent hover:text-accent">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('Add') }}
        </button>
    </div>

    <div data-faq-list class="mt-4 space-y-3">
        @foreach ($rows as $index => $item)
            <div data-faq-row class="rounded-xl border border-line bg-surface/60 p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-1 space-y-3">
                        <input name="faq[{{ $index }}][question]" value="{{ $item['question'] ?? '' }}" maxlength="200" placeholder="{{ __('Question') }}" class="{{ $input }}">
                        <textarea name="faq[{{ $index }}][answer]" rows="2" maxlength="1000" placeholder="{{ __('Answer') }}" class="{{ $input }}">{{ $item['answer'] ?? '' }}</textarea>
                        @error("faq.$index.question")<p class="text-sm text-error">{{ $message }}</p>@enderror
                        @error("faq.$index.answer")<p class="text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <button type="button" data-faq-remove aria-label="{{ __('Remove question') }}" class="flex size-9 shrink-0 cursor-pointer items-center justify-center rounded-lg text-muted hover:bg-error/10 hover:text-error">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</section>
