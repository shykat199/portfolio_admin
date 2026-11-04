<x-layouts.app :title="__('Testimonials')">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-muted">{{ trans_choice(':count testimonial|:count testimonials', $testimonials->count()) }} · {{ __('shown in the slider in this order') }}</p>
        <a href="{{ route('testimonials.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('Add testimonial') }}
        </a>
    </div>

    @if ($testimonials->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-line p-10 text-center">
            <p class="font-medium">{{ __('No testimonials yet') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('The testimonials section stays hidden on the website until you add one. Only add real feedback from real clients.') }}</p>
        </div>
    @else
        <ul class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($testimonials as $testimonial)
                <li class="flex flex-col rounded-2xl border border-line bg-raised p-5 sm:p-6 {{ $testimonial->is_active ? '' : 'opacity-60' }}">
                    <div class="flex items-center justify-between gap-3">
                        @if ($testimonial->rating)
                            <span class="text-sm tracking-widest text-warm" aria-label="{{ __('Rated :n out of 5', ['n' => $testimonial->rating]) }}">{{ str_repeat('★', $testimonial->rating) }}<span class="text-fg/15">{{ str_repeat('★', 5 - $testimonial->rating) }}</span></span>
                        @else
                            <span class="text-xs text-muted">{{ __('No rating') }}</span>
                        @endif
                        <span class="text-xs text-muted">#{{ $testimonial->sort_order }} · {{ $testimonial->is_active ? __('Shown') : __('Hidden') }}</span>
                    </div>
                    <blockquote class="mt-4 line-clamp-4 flex-1 text-[0.95rem] leading-relaxed">“{{ $testimonial->quote }}”</blockquote>
                    <div class="mt-5 flex items-center gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-accent/15 font-mono text-xs font-semibold text-accent">
                            @if ($testimonial->photo_url)
                                <img src="{{ $testimonial->photo_url }}" alt="" class="size-full object-cover">
                            @else
                                {{ Str::of($testimonial->name)->explode(' ')->take(2)->map(fn ($w) => Str::upper(Str::substr($w, 0, 1)))->implode('') }}
                            @endif
                        </span>
                        <div class="min-w-0 text-sm">
                            <p class="truncate font-medium">{{ $testimonial->name }}</p>
                            <p class="truncate text-muted">{{ $testimonial->role }}</p>
                        </div>
                    </div>
                    <div class="mt-5 flex items-center gap-2 border-t border-line pt-4">
                        <a href="{{ route('testimonials.edit', $testimonial) }}" class="inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm hover:border-accent hover:text-accent">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('testimonials.destroy', $testimonial) }}" class="ml-auto" data-confirm="{{ __('Delete the testimonial from :name?', ['name' => $testimonial->name]) }}">
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
