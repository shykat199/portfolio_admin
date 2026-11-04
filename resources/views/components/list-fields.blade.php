{{-- A list of single-line items with an "Add" button, e.g. reasons or typed words. --}}
@props(['name', 'label', 'items' => [], 'hint' => null, 'placeholder' => '', 'addLabel' => null, 'max' => 10])

@php
    $rows = array_values(array_filter((array) $items, fn ($item) => is_string($item))) ?: [''];
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
@endphp

<div data-list data-list-max="{{ $max }}">
    <p class="text-sm font-medium">{{ $label }}</p>
    @if ($hint)
        <p class="mt-0.5 text-xs text-muted">{{ $hint }}</p>
    @endif

    <ol data-list-rows class="mt-2 space-y-2">
        @foreach ($rows as $item)
            <li data-list-row class="flex items-center gap-2">
                <span aria-hidden="true" class="w-5 shrink-0 text-right font-mono text-xs text-muted" data-list-number>{{ $loop->iteration }}</span>
                <input name="{{ $name }}[]" value="{{ $item }}" maxlength="200" placeholder="{{ $placeholder }}" aria-label="{{ $label }} {{ $loop->iteration }}" class="{{ $input }}">
                <button type="button" data-list-remove aria-label="{{ __('Remove') }}" class="flex size-10 shrink-0 cursor-pointer items-center justify-center rounded-lg text-muted hover:bg-error/10 hover:text-error">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </li>
        @endforeach
    </ol>

    <button type="button" data-list-add class="mt-2 ml-7 inline-flex min-h-9 cursor-pointer items-center gap-1.5 rounded-lg border border-dashed border-line px-3 text-sm text-muted hover:border-accent hover:text-accent">
        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        {{ $addLabel ?? __('Add more') }}
    </button>

    @error($name)<p class="mt-1.5 text-sm text-error">{{ $message }}</p>@enderror
    @error($name.'.*')<p class="mt-1.5 text-sm text-error">{{ $message }}</p>@enderror
</div>
