@props(['href', 'active' => false, 'icon'])

<a href="{{ $href }}" @if ($active) aria-current="page" @endif
    {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors '.($active ? 'bg-accent/15 text-accent' : 'text-muted hover:bg-fg/5 hover:text-fg')]) }}>
    <svg viewBox="0 0 24 24" class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="{{ $icon }}"/>
    </svg>
    <span class="flex-1">{{ $slot }}</span>
</a>
