@php
    $editing = $service->exists;
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
    $lines = fn (?array $items): string => implode("\n", $items ?? []);
@endphp

<x-layouts.app :title="$editing ? __('Edit service') : __('New service')">
    <form method="POST" action="{{ $editing ? route('services.update', $service) : route('services.store') }}" novalidate class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="space-y-6 xl:col-span-2">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <h2 class="font-medium">{{ __('Package card') }} <span class="text-xs font-normal text-muted">· {{ __('shown in the Services section of the home page') }}</span></h2>
                <x-field name="name" :label="__('Name')">
                    <input id="name" name="name" value="{{ old('name', $service->name) }}" required maxlength="80" data-slug-source placeholder="{{ __('e.g. Business website') }}" class="{{ $input }} text-lg! font-medium">
                </x-field>
                <x-field name="slug" :label="__('Web address')" :hint="$editing ? __('Changing this breaks existing links to the page.') : __('Created from the name as you type. You can change it.')">
                    <div class="flex items-center overflow-hidden rounded-lg border border-fg/20 bg-surface focus-within:border-accent">
                        <span class="pl-3.5 font-mono text-xs text-muted">/services/</span>
                        <input id="slug" name="slug" value="{{ old('slug', $service->slug) }}" maxlength="160" data-slug-target data-auto="{{ $editing || old('slug') ? 'false' : 'true' }}" class="w-full bg-transparent px-1 py-2.5 font-mono text-sm focus:outline-none" placeholder="business-website">
                    </div>
                </x-field>
                <x-field name="for_who" :label="__('Short description')" :hint="__('Who the package is for, in one sentence.')">
                    <textarea id="for_who" name="for_who" rows="2" maxlength="300" required placeholder="{{ __('e.g. For businesses that need a professional website that brings in enquiries.') }}" class="{{ $input }}">{{ old('for_who', $service->for_who) }}</textarea>
                </x-field>
                <x-field name="includes" :label="__('What’s included')" :hint="__('One item per line.')">
                    <textarea id="includes" name="includes" rows="5" required placeholder="{{ __("Up to 5 custom-designed pages\nLooks great on phones\nSet up to be found on Google") }}" class="{{ $input }}">{{ old('includes', $lines($service->includes)) }}</textarea>
                </x-field>
            </section>

            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <h2 class="font-medium">{{ __('Service page') }} <span class="text-xs font-normal text-muted">· /services/{{ $service->slug ?: '…' }}</span></h2>
                <x-field name="heading" :label="__('Page heading')">
                    <input id="heading" name="heading" value="{{ old('heading', $service->heading) }}" required maxlength="160" data-seo-fallback="title" placeholder="{{ __('e.g. Website design for small businesses') }}" class="{{ $input }}">
                </x-field>
                <x-field name="intro" :label="__('Page intro')" :hint="__('Shown under the heading. Aim for 120 to 160 characters.')">
                    <textarea id="intro" name="intro" rows="3" maxlength="320" required data-seo-fallback="description" placeholder="{{ __('A short introduction to the service and its main benefit.') }}" class="{{ $input }}">{{ old('intro', $service->intro) }}</textarea>
                </x-field>
                <x-field name="story" :label="__('About the service')" :hint="__('Leave an empty line between paragraphs.')">
                    <textarea id="story" name="story" rows="8" required placeholder="{{ __('Explain the problem this service solves and how you help…') }}" class="{{ $input }}">{{ old('story', $service->story) }}</textarea>
                </x-field>
                <x-field name="ideal_for" :label="__('Ideal for')" :hint="__('One item per line.')">
                    <textarea id="ideal_for" name="ideal_for" rows="4" placeholder="{{ __("Local businesses without a website\nClinics, salons and trades") }}" class="{{ $input }}">{{ old('ideal_for', $lines($service->ideal_for)) }}</textarea>
                </x-field>
                <x-field name="related_projects" :label="__('Related projects')" :hint="__('Case studies shown on this service page.')">
                    @php($chosen = old('related_projects', $service->related_projects ?? []))
                    @forelse ($projects as $slug => $title)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line bg-surface/60 px-3.5 py-2.5 text-sm hover:border-fg/30 [&:not(:first-child)]:mt-2">
                            <input type="checkbox" name="related_projects[]" value="{{ $slug }}" @checked(in_array($slug, $chosen, true)) class="size-4 accent-accent">
                            {{ $title }}
                        </label>
                    @empty
                        <p class="text-sm text-muted">{{ __('No projects yet.') }} <a href="{{ route('projects.create') }}" class="text-accent hover:underline">{{ __('Add one') }}</a></p>
                    @endforelse
                </x-field>
            </section>

            <x-faq-fields :items="old('faq', $service->faq ?? [])" :hint="__('Shown on the service page and sent to Google. Empty rows are ignored.')" />

            <x-seo-fields
                path="services"
                :slug="$service->slug"
                :title="old('seo_title', $service->seo_title)"
                :description="old('meta_description', $service->meta_description)"
                :keywords="old('meta_keywords', implode(', ', $service->meta_keywords ?? []))"
                :hint="__('How the page appears in Google. Empty fields use the page heading, intro and name.')"
                :title-placeholder="__('Uses the page heading if empty')"
                :description-placeholder="__('Uses the page intro if empty')"
                :keywords-hint="__('Separate with commas. Uses the name if empty.')" />
        </div>

        <div class="space-y-6">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active)) class="mt-0.5 size-4 accent-accent">
                    <span>
                        <span class="block text-sm font-medium">{{ __('Show on the website') }}</span>
                        <span class="block text-xs text-muted">{{ __('Untick to hide the card and its page without deleting it.') }}</span>
                    </span>
                </label>
                <x-field name="sort_order" :label="__('Display order')" :hint="__('Lower numbers appear first.')">
                    <input id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $service->sort_order) }}" class="{{ $input }}">
                </x-field>

                <div class="flex flex-col gap-2 border-t border-line pt-5 sm:flex-row xl:flex-col">
                    <button type="submit" class="inline-flex min-h-11 flex-1 cursor-pointer items-center justify-center rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                        {{ $editing ? __('Save changes') : __('Create service') }}
                    </button>
                    <a href="{{ route('services.index') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-full border border-line px-5 text-sm text-muted hover:text-fg">{{ __('Back to services') }}</a>
                </div>
                @if ($editing && $service->is_active && config('blog.frontend_url'))
                    <a href="{{ rtrim(config('blog.frontend_url'), '/') }}/services/{{ $service->slug }}" target="_blank" rel="noopener" class="block text-center text-sm text-accent hover:underline">{{ __('View on website ↗') }}</a>
                @endif
            </section>

            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <h2 class="font-medium">{{ __('Price and timing') }}</h2>
                <x-field name="price" :label="__('Starting price (£)')">
                    <input id="price" name="price" type="number" min="0" step="1" value="{{ old('price', $service->price) }}" required placeholder="695" class="{{ $input }}">
                </x-field>
                <x-field name="price_suffix" :label="__('After the price')" :hint="__('Optional, e.g. /month')">
                    <input id="price_suffix" name="price_suffix" value="{{ old('price_suffix', $service->price_suffix) }}" maxlength="20" placeholder="/month" class="{{ $input }}">
                </x-field>
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="hidden" name="billed_monthly" value="0">
                    <input type="checkbox" name="billed_monthly" value="1" @checked(old('billed_monthly', $service->billed_monthly)) class="mt-0.5 size-4 accent-accent">
                    <span>
                        <span class="block text-sm font-medium">{{ __('Billed monthly') }}</span>
                        <span class="block text-xs text-muted">{{ __('Tells Google the price is per month.') }}</span>
                    </span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <x-field name="time_label" :label="__('Time label')">
                        <input id="time_label" name="time_label" value="{{ old('time_label', $service->time_label) }}" required maxlength="40" placeholder="{{ __('Delivery') }}" class="{{ $input }}">
                    </x-field>
                    <x-field name="time" :label="__('Time')">
                        <input id="time" name="time" value="{{ old('time', $service->time) }}" required maxlength="80" placeholder="{{ __('2 to 3 weeks') }}" class="{{ $input }}">
                    </x-field>
                </div>
            </section>
        </div>
    </form>

    @if ($editing)
        <form method="POST" action="{{ route('services.destroy', $service) }}" class="mt-6" data-confirm="{{ __('Delete this service? Blog posts linked to it will be unlinked. This cannot be undone.') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-sm text-muted hover:text-error">{{ __('Delete this service') }}</button>
        </form>
    @endif
</x-layouts.app>
