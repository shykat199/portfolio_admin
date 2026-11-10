@php
    $editing = $project->exists;
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
    $lines = fn (?array $items): string => implode("\n", $items ?? []);
    $shots = old('screenshots') ? collect(old('screenshots'))->filter(fn ($row) => filled($row['path'] ?? null))->values()->all() : ($project->screenshots ?? []);
    $shotUrl = fn (string $path): string => Storage::disk('public')->url($path);
@endphp

<x-layouts.app :title="$editing ? __('Edit project') : __('New project')">
    <form method="POST" action="{{ $editing ? route('projects.update', $project) : route('projects.store') }}" enctype="multipart/form-data" novalidate class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="space-y-6 xl:col-span-2">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <h2 class="font-medium">{{ __('Work card') }} <span class="text-xs font-normal text-muted">· {{ __('shown in the Work section of the home page') }}</span></h2>
                <x-field name="title" :label="__('Title')">
                    <input id="title" name="title" value="{{ old('title', $project->title) }}" required maxlength="160" data-slug-source data-seo-fallback="title" placeholder="{{ __('e.g. Website for a local clinic') }}" class="{{ $input }} text-lg! font-medium">
                </x-field>
                <x-field name="slug" :label="__('Web address')" :hint="$editing ? __('Changing this breaks existing links to the case study.') : __('Created from the title as you type. You can change it.')">
                    <div class="flex items-center overflow-hidden rounded-lg border border-fg/20 bg-surface focus-within:border-accent">
                        <span class="pl-3.5 font-mono text-xs text-muted">/work/</span>
                        <input id="slug" name="slug" value="{{ old('slug', $project->slug) }}" maxlength="160" data-slug-target data-auto="{{ $editing || old('slug') ? 'false' : 'true' }}" class="w-full bg-transparent px-1 py-2.5 font-mono text-sm focus:outline-none" placeholder="clinic-website">
                    </div>
                </x-field>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-field name="category" :label="__('Category')">
                        <input id="category" name="category" value="{{ old('category', $project->category) }}" required maxlength="60" placeholder="{{ __('e.g. Clinic') }}" class="{{ $input }}">
                    </x-field>
                    <x-field name="year" :label="__('Year')">
                        <input id="year" name="year" value="{{ old('year', $project->year) }}" required inputmode="numeric" maxlength="4" placeholder="{{ now()->format('Y') }}" class="{{ $input }}">
                    </x-field>
                </div>
                <x-field name="live_url" :label="__('Live project URL')" :hint="__('Optional. The link opens in a new tab on the case study page.')">
                    <input id="live_url" name="live_url" type="url" value="{{ old('live_url', $project->live_url) }}" maxlength="2048" placeholder="https://example.com" class="{{ $input }}">
                </x-field>
                <x-field name="problem" :label="__('Problem')" :hint="__('One or two sentences.')">
                    <textarea id="problem" name="problem" rows="2" maxlength="300" required placeholder="{{ __('What was getting in the client’s way?') }}" class="{{ $input }}">{{ old('problem', $project->problem) }}</textarea>
                </x-field>
                <x-field name="solution" :label="__('Solution')">
                    <textarea id="solution" name="solution" rows="2" maxlength="300" required placeholder="{{ __('What you built to fix it.') }}" class="{{ $input }}">{{ old('solution', $project->solution) }}</textarea>
                </x-field>
                <x-field name="result" :label="__('Result')">
                    <textarea id="result" name="result" rows="2" maxlength="300" required placeholder="{{ __('What changed for the client.') }}" class="{{ $input }}">{{ old('result', $project->result) }}</textarea>
                </x-field>
            </section>

            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <h2 class="font-medium">{{ __('Case study page') }} <span class="text-xs font-normal text-muted">· /work/{{ $project->slug ?: '…' }}</span></h2>
                <x-field name="overview" :label="__('Overview')">
                    <textarea id="overview" name="overview" rows="4" maxlength="2000" required data-seo-fallback="description" placeholder="{{ __('A short introduction to the project and the brief.') }}" class="{{ $input }}">{{ old('overview', $project->overview) }}</textarea>
                </x-field>
                <x-field name="client_problem" :label="__('The client’s problem')">
                    <textarea id="client_problem" name="client_problem" rows="4" maxlength="2000" required placeholder="{{ __('Describe the situation before you started.') }}" class="{{ $input }}">{{ old('client_problem', $project->client_problem) }}</textarea>
                </x-field>
                <x-field name="built" :label="__('What I built')" :hint="__('One item per line.')">
                    <textarea id="built" name="built" rows="5" required placeholder="{{ __("A home page that shows opening hours straight away\nA page for each treatment") }}" class="{{ $input }}">{{ old('built', $lines($project->built)) }}</textarea>
                </x-field>
                <x-field name="tech" :label="__('Tech used')" :hint="__('Separate with commas.')">
                    <input id="tech" name="tech" value="{{ old('tech', implode(', ', $project->tech ?? [])) }}" placeholder="Laravel, React, MySQL" class="{{ $input }}">
                </x-field>
                <x-field name="results" :label="__('Results')" :hint="__('One item per line.')">
                    <textarea id="results" name="results" rows="4" required placeholder="{{ __("Fewer phone calls about opening times\nMore online enquiries") }}" class="{{ $input }}">{{ old('results', $lines($project->results)) }}</textarea>
                </x-field>
            </section>

            {{-- Screenshots --}}
            <section class="rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-medium">{{ __('Screenshots') }}</h2>
                        <p class="text-xs text-muted">{{ __('JPG, PNG or WebP, up to 4 MB each. Shown in a grid on the case study page.') }}</p>
                    </div>
                    <button type="button" data-shot-add class="inline-flex min-h-9 shrink-0 cursor-pointer items-center gap-1.5 rounded-lg border border-line px-3 text-sm hover:border-accent hover:text-accent">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        {{ __('Add screenshot') }}
                    </button>
                </div>

                <div data-shot-list class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach ($shots as $index => $shot)
                        <div data-shot-row class="overflow-hidden rounded-xl border border-line bg-surface/60">
                            <input type="hidden" name="screenshots[{{ $index }}][path]" value="{{ $shot['path'] }}">
                            <input type="hidden" name="screenshots[{{ $index }}][remove]" value="0" data-shot-remove-input>
                            <div class="relative aspect-[16/10] border-b border-line bg-surface">
                                <img data-shot-preview src="{{ $shotUrl($shot['path']) }}" alt="" class="size-full object-cover">
                                <button type="button" data-shot-remove class="absolute top-2 right-2 inline-flex cursor-pointer items-center gap-1 rounded-lg bg-black/70 px-2.5 py-1 text-xs text-fg backdrop-blur hover:bg-error hover:text-surface">{{ __('Remove') }}</button>
                            </div>
                            <div class="space-y-3 p-3">
                                <label class="block text-xs text-muted">{{ __('Replace image') }}
                                    <input type="file" name="screenshots[{{ $index }}][file]" accept="image/jpeg,image/png,image/webp" data-shot-file class="mt-1 block w-full text-xs file:mr-2 file:cursor-pointer file:rounded-full file:border-0 file:bg-fg/10 file:px-3 file:py-1.5 file:text-xs file:text-fg">
                                </label>
                                <input name="screenshots[{{ $index }}][alt]" value="{{ $shot['alt'] ?? '' }}" maxlength="200" placeholder="{{ __('Image description (required)') }}" class="{{ $input }}">
                                @error("screenshots.$index.alt")<p class="text-xs text-error">{{ $message }}</p>@enderror
                                <input name="screenshots[{{ $index }}][caption]" value="{{ $shot['caption'] ?? '' }}" maxlength="200" placeholder="{{ __('Caption shown under the image') }}" class="{{ $input }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('screenshots')<p class="mt-2 text-sm text-error">{{ $message }}</p>@enderror

                <template data-shot-template>
                    <div data-shot-row data-new class="overflow-hidden rounded-xl border border-dashed border-accent/40 bg-surface/60">
                        <div class="relative aspect-[16/10] border-b border-line bg-surface">
                            <img data-shot-preview alt="" class="hidden size-full object-cover">
                            <span data-shot-empty class="absolute inset-0 flex items-center justify-center text-xs text-muted">{{ __('Choose an image below') }}</span>
                            <button type="button" data-shot-remove class="absolute top-2 right-2 cursor-pointer rounded-lg bg-black/70 px-2.5 py-1 text-xs text-fg backdrop-blur hover:bg-error hover:text-surface">{{ __('Remove') }}</button>
                        </div>
                        <div class="space-y-3 p-3">
                            <input type="file" name="screenshots[__INDEX__][file]" accept="image/jpeg,image/png,image/webp" data-shot-file class="block w-full text-xs file:mr-2 file:cursor-pointer file:rounded-full file:border-0 file:bg-fg/10 file:px-3 file:py-1.5 file:text-xs file:text-fg">
                            <input name="screenshots[__INDEX__][alt]" maxlength="200" placeholder="{{ __('Image description (required)') }}" class="{{ $input }}">
                            <input name="screenshots[__INDEX__][caption]" maxlength="200" placeholder="{{ __('Caption shown under the image') }}" class="{{ $input }}">
                        </div>
                    </div>
                </template>
            </section>

            <x-seo-fields
                path="work"
                :slug="$project->slug"
                :title="old('seo_title', $project->seo_title)"
                :description="old('meta_description', $project->meta_description)"
                :keywords="old('meta_keywords', implode(', ', $project->meta_keywords ?? []))"
                :hint="__('How the case study appears in Google. Empty fields use the title, overview and tech used.')"
                :title-placeholder="__('Uses the title if empty')"
                :description-placeholder="__('Uses the overview if empty')"
                :keywords-hint="__('Separate with commas. Uses the tech used if empty.')" />
        </div>

        <div class="space-y-6">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $project->is_active)) class="mt-0.5 size-4 accent-accent">
                    <span>
                        <span class="block text-sm font-medium">{{ __('Show on the website') }}</span>
                        <span class="block text-xs text-muted">{{ __('Untick to hide the project without deleting it.') }}</span>
                    </span>
                </label>
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="hidden" name="is_demo" value="0">
                    <input type="checkbox" name="is_demo" value="1" @checked(old('is_demo', $project->is_demo)) class="mt-0.5 size-4 accent-accent">
                    <span>
                        <span class="block text-sm font-medium">{{ __('Demo project') }}</span>
                        <span class="block text-xs text-muted">{{ __('Shows a visible “Demo project” label. Untick for real client work.') }}</span>
                    </span>
                </label>
                <x-field name="sort_order" :label="__('Display order')" :hint="__('Lower numbers appear first.')">
                    <input id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $project->sort_order) }}" class="{{ $input }}">
                </x-field>
                <x-field name="service" :label="__('Related service')">
                    <select id="service" name="service" class="{{ $input }}">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($services as $slug => $label)
                            <option value="{{ $slug }}" @selected(old('service', $project->service) === $slug)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>

                <div class="flex flex-col gap-2 border-t border-line pt-5 sm:flex-row xl:flex-col">
                    <button type="submit" class="inline-flex min-h-11 flex-1 cursor-pointer items-center justify-center rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                        {{ $editing ? __('Save changes') : __('Create project') }}
                    </button>
                    <a href="{{ route('projects.index') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-full border border-line px-5 text-sm text-muted hover:text-fg">{{ __('Back to projects') }}</a>
                </div>
                @if ($editing && $project->is_active && config('blog.frontend_url'))
                    <a href="{{ rtrim(config('blog.frontend_url'), '/') }}/work/{{ $project->slug }}" target="_blank" rel="noopener" class="block text-center text-sm text-accent hover:underline">{{ __('View on website ↗') }}</a>
                @endif
            </section>

            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <x-field name="cover" :label="__('Cover image')" :hint="__('JPG, PNG or WebP, up to 4 MB. Best at 1600 × 1000.')">
                    <div class="aspect-[16/10] overflow-hidden rounded-lg border border-dashed border-line bg-surface">
                        <img data-cover-preview src="{{ $project->cover_url }}" alt="" class="size-full object-cover {{ $project->cover_url ? '' : 'hidden' }}">
                    </div>
                    <input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp" data-cover-input
                        class="mt-3 block w-full text-sm text-muted file:mr-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-fg/10 file:px-4 file:py-2 file:text-sm file:text-fg hover:file:bg-fg/15">
                </x-field>
                <x-field name="cover_alt" :label="__('Cover description')" :hint="__('Describe the image for screen readers and Google.')">
                    <input id="cover_alt" name="cover_alt" value="{{ old('cover_alt', $project->cover_alt) }}" required maxlength="200" placeholder="{{ __('e.g. The clinic website shown on a laptop') }}" class="{{ $input }}">
                </x-field>
            </section>
        </div>
    </form>

    @if ($editing)
        <form method="POST" action="{{ route('projects.destroy', $project) }}" class="mt-6" data-confirm="{{ __('Delete this project and its images? This cannot be undone.') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-sm text-muted hover:text-error">{{ __('Delete this project') }}</button>
        </form>
    @endif
</x-layouts.app>
