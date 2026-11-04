@props(['post'])

@php
    $live = $post->isLive();
    $scheduled = $post->status === \App\PostStatus::Published && ! $live;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs '.($live ? 'border-accent/40 bg-accent/10 text-accent' : ($scheduled ? 'border-warm/40 bg-warm/10 text-warm' : 'border-line text-muted'))]) }}>
    <span class="size-1.5 rounded-full {{ $live ? 'bg-accent' : ($scheduled ? 'bg-warm' : 'bg-muted') }}"></span>
    {{ $live ? __('Published') : ($scheduled ? __('Scheduled') : __('Draft')) }}
</span>
