@props(['name', 'label', 'hint' => null])

@php($errorKey = str_replace(['[', ']'], ['.', ''], $name))

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="text-sm font-medium">{{ $label }}</label>
    @if ($hint)
        <p class="mt-0.5 text-xs text-muted">{{ $hint }}</p>
    @endif
    <div class="mt-2">{{ $slot }}</div>
    @error($errorKey)
        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
    @enderror
</div>
