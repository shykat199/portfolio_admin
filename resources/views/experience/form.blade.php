@php
    $editing = $job->exists;
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
    $isCurrent = (bool) old('is_current', $editing ? $job->isCurrent() : true);
@endphp

<x-layouts.app :title="$editing ? __('Edit job') : __('Add job')">
    <form method="POST" action="{{ $editing ? route('experience.update', $job) : route('experience.store') }}" novalidate class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="space-y-6 xl:col-span-2">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-field name="role" :label="__('Job title')">
                        <input id="role" name="role" value="{{ old('role', $job->role) }}" required maxlength="120" placeholder="{{ __('e.g. Full-Stack Developer') }}" class="{{ $input }}">
                    </x-field>
                    <x-field name="company" :label="__('Company')">
                        <input id="company" name="company" value="{{ old('company', $job->company) }}" required maxlength="120" placeholder="{{ __('e.g. PFEC Global') }}" class="{{ $input }}">
                    </x-field>
                    <x-field name="company_url" :label="__('Company website')" :hint="__('Optional.')">
                        <input id="company_url" name="company_url" type="url" value="{{ old('company_url', $job->company_url) }}" maxlength="255" placeholder="https://company.com" class="{{ $input }}">
                    </x-field>
                    <x-field name="location" :label="__('Location')" :hint="__('Optional.')">
                        <input id="location" name="location" value="{{ old('location', $job->location) }}" maxlength="120" placeholder="{{ __('e.g. Dhaka, Bangladesh') }}" class="{{ $input }}">
                    </x-field>
                </div>
                <x-field name="summary" :label="__('What you do there')">
                    <textarea id="summary" name="summary" rows="3" maxlength="1000" required placeholder="{{ __('One or two sentences about your role.') }}" class="{{ $input }}">{{ old('summary', $job->summary) }}</textarea>
                </x-field>
                <x-field name="highlights" :label="__('Highlights')" :hint="__('Optional. One achievement per line.')">
                    <textarea id="highlights" name="highlights" rows="4" placeholder="{{ __("Built the student application portal\nCut page load times in half") }}" class="{{ $input }}">{{ old('highlights', implode("\n", $job->highlights ?? [])) }}</textarea>
                </x-field>
                <x-field name="tech" :label="__('Technologies')" :hint="__('Separate with commas.')">
                    <input id="tech" name="tech" value="{{ old('tech', implode(', ', $job->tech ?? [])) }}" placeholder="Laravel, React, MySQL" class="{{ $input }}">
                </x-field>
            </section>
        </div>

        <div class="space-y-6">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <x-field name="type" :label="__('Job type')">
                    <input id="type" name="type" value="{{ old('type', $job->type) }}" required maxlength="40" list="job-types" placeholder="{{ __('Full-time') }}" class="{{ $input }}">
                    <datalist id="job-types">
                        <option value="Full-time"><option value="Part-time"><option value="Contract"><option value="Freelance"><option value="Internship">
                    </datalist>
                </x-field>
                <div class="grid grid-cols-2 gap-3">
                    <x-field name="start" :label="__('Started')">
                        <input id="start" name="start" value="{{ old('start', $job->start) }}" required maxlength="40" placeholder="{{ __('Mar 2025') }}" class="{{ $input }}">
                    </x-field>
                    <x-field name="end" :label="__('Ended')">
                        <input id="end" name="end" value="{{ old('end', $job->end) }}" maxlength="40" placeholder="{{ __('Jun 2026') }}" data-end-input @disabled($isCurrent) class="{{ $input }} disabled:opacity-40">
                    </x-field>
                </div>
                <label class="flex cursor-pointer items-center gap-3 text-sm">
                    <input type="hidden" name="is_current" value="0">
                    <input type="checkbox" name="is_current" value="1" data-current-toggle @checked($isCurrent) class="size-4 accent-accent">
                    {{ __('I work here now (shows “Present” and a “Now” badge)') }}
                </label>

                <div class="border-t border-line pt-5">
                    <x-field name="sort_order" :label="__('Display order')" :hint="__('Lower numbers appear first. Put your current job first.')">
                        <input id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $job->sort_order) }}" class="{{ $input }}">
                    </x-field>
                </div>
                <label class="flex cursor-pointer items-center gap-3 text-sm">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $job->is_active)) class="size-4 accent-accent">
                    {{ __('Show on the website') }}
                </label>

                <div class="flex flex-col gap-2 border-t border-line pt-5">
                    <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                        {{ $editing ? __('Save changes') : __('Add job') }}
                    </button>
                    <a href="{{ route('experience.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-line px-5 text-sm text-muted hover:text-fg">{{ __('Back to experience') }}</a>
                </div>
            </section>
        </div>
    </form>
</x-layouts.app>
