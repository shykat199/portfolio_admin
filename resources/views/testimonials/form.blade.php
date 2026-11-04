@php
    $editing = $testimonial->exists;
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
    $rating = (string) old('rating', $testimonial->rating);
@endphp

<x-layouts.app :title="$editing ? __('Edit testimonial') : __('Add testimonial')">
    <form method="POST" action="{{ $editing ? route('testimonials.update', $testimonial) : route('testimonials.store') }}" enctype="multipart/form-data" novalidate class="max-w-2xl space-y-6">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
            <x-field name="quote" :label="__('What they said')" :hint="__('Use the client’s own words. Only add real feedback.')">
                <textarea id="quote" name="quote" rows="5" maxlength="1500" required placeholder="{{ __('e.g. Shykat built our booking website in four weeks and we now get bookings while we sleep.') }}" class="{{ $input }}">{{ old('quote', $testimonial->quote) }}</textarea>
            </x-field>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="name" :label="__('Name')">
                    <input id="name" name="name" value="{{ old('name', $testimonial->name) }}" required maxlength="100" placeholder="{{ __('e.g. Sarah Thompson') }}" class="{{ $input }}">
                </x-field>
                <x-field name="role" :label="__('Role and company')">
                    <input id="role" name="role" value="{{ old('role', $testimonial->role) }}" required maxlength="150" placeholder="{{ __('e.g. Owner, Thompson Salon') }}" class="{{ $input }}">
                </x-field>
            </div>

            <fieldset>
                <legend class="text-sm font-medium">{{ __('Star rating') }}</legend>
                <p class="mt-0.5 text-xs text-muted">{{ __('Only if the client actually gave one.') }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="" class="peer sr-only" @checked($rating === '')>
                        <span class="inline-flex min-h-10 items-center rounded-lg border border-line px-3 text-sm text-muted peer-checked:border-accent peer-checked:text-accent peer-focus-visible:outline-2 peer-focus-visible:outline-accent">{{ __('No rating') }}</span>
                    </label>
                    @foreach (range(5, 1) as $stars)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $stars }}" class="peer sr-only" @checked($rating === (string) $stars)>
                            <span class="inline-flex min-h-10 items-center rounded-lg border border-line px-3 text-sm tracking-widest text-warm/60 peer-checked:border-warm peer-checked:text-warm peer-focus-visible:outline-2 peer-focus-visible:outline-accent">{{ str_repeat('★', $stars) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('rating')<p class="mt-1.5 text-sm text-error">{{ $message }}</p>@enderror
            </fieldset>

            <x-field name="photo" :label="__('Photo')" :hint="__('Optional. JPG, PNG or WebP, up to 2 MB. Without one, their initials are shown.')">
                <div class="flex items-center gap-4">
                    <span class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-full border border-line bg-surface">
                        <img data-cover-preview src="{{ $testimonial->photo_url }}" alt="" class="size-full object-cover {{ $testimonial->photo_url ? '' : 'hidden' }}">
                    </span>
                    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" data-cover-input
                        class="block w-full text-sm text-muted file:mr-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-fg/10 file:px-4 file:py-2 file:text-sm file:text-fg hover:file:bg-fg/15">
                </div>
                @if ($testimonial->photo_url)
                    <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm text-muted">
                        <input type="checkbox" name="remove_photo" value="1" class="size-4 accent-accent">
                        {{ __('Remove the current photo') }}
                    </label>
                @endif
            </x-field>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="sort_order" :label="__('Display order')" :hint="__('Lower numbers appear first.')">
                    <input id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $testimonial->sort_order) }}" class="{{ $input }}">
                </x-field>
                <label class="flex cursor-pointer items-start gap-3 pt-7">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial->is_active)) class="mt-0.5 size-4 accent-accent">
                    <span class="text-sm font-medium">{{ __('Show on the website') }}</span>
                </label>
            </div>
        </section>

        <div class="flex flex-col gap-2 sm:flex-row">
            <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-full bg-accent px-6 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                {{ $editing ? __('Save changes') : __('Add testimonial') }}
            </button>
            <a href="{{ route('testimonials.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-line px-6 text-sm text-muted hover:text-fg">{{ __('Back to testimonials') }}</a>
        </div>
    </form>
</x-layouts.app>
