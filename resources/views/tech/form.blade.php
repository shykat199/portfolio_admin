@php
    $editing = $item->exists;
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
@endphp

<x-layouts.app :title="$editing ? __('Edit technology') : __('Add technology')">
    <form method="POST" action="{{ $editing ? route('tech.update', $item) : route('tech.store') }}" enctype="multipart/form-data" novalidate class="max-w-xl space-y-6">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
            <x-field name="name" :label="__('Name')">
                <input id="name" name="name" value="{{ old('name', $item->name) }}" required maxlength="60" placeholder="{{ __('e.g. Laravel') }}" class="{{ $input }} text-lg! font-medium">
            </x-field>

            <x-field name="logo" :label="__('Logo')" :hint="__('SVG, PNG, JPG or WebP, up to 1 MB. A square logo on a transparent background looks best. Without one, the first two letters are shown.')">
                <div class="flex items-center gap-4">
                    <span class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-full border border-line bg-surface">
                        <img data-cover-preview src="{{ $item->logo_url }}" alt="" class="size-10 object-contain {{ $item->logo_url ? '' : 'hidden' }}">
                    </span>
                    <input id="logo" name="logo" type="file" accept="image/svg+xml,image/png,image/jpeg,image/webp" data-cover-input
                        class="block w-full text-sm text-muted file:mr-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-fg/10 file:px-4 file:py-2 file:text-sm file:text-fg hover:file:bg-fg/15">
                </div>
                @if ($item->logo_url)
                    <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm text-muted">
                        <input type="checkbox" name="remove_logo" value="1" class="size-4 accent-accent">
                        {{ __('Remove the current logo') }}
                    </label>
                @endif
            </x-field>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="sort_order" :label="__('Display order')" :hint="__('Lower numbers appear first.')">
                    <input id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $item->sort_order) }}" class="{{ $input }}">
                </x-field>
                <label class="flex cursor-pointer items-start gap-3 pt-7">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active)) class="mt-0.5 size-4 accent-accent">
                    <span class="text-sm font-medium">{{ __('Show on the website') }}</span>
                </label>
            </div>
        </section>

        <div class="flex flex-col gap-2 sm:flex-row">
            <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-full bg-accent px-6 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                {{ $editing ? __('Save changes') : __('Add technology') }}
            </button>
            <a href="{{ route('tech.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-line px-6 text-sm text-muted hover:text-fg">{{ __('Back to tech stack') }}</a>
        </div>
    </form>
</x-layouts.app>
