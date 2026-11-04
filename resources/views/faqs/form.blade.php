@php
    $editing = $faq->exists;
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
@endphp

<x-layouts.app :title="$editing ? __('Edit question') : __('Add question')">
    <form method="POST" action="{{ $editing ? route('faqs.update', $faq) : route('faqs.store') }}" novalidate class="max-w-2xl space-y-6">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
            <x-field name="question" :label="__('Question')">
                <input id="question" name="question" value="{{ old('question', $faq->question) }}" required maxlength="200" placeholder="{{ __('e.g. How long does a project take?') }}" class="{{ $input }}">
            </x-field>
            <x-field name="answer" :label="__('Answer')" :hint="__('Plain, direct answers work best, and are also sent to Google.')">
                <textarea id="answer" name="answer" rows="6" maxlength="2000" required placeholder="{{ __('e.g. A business website usually takes 2 to 3 weeks.') }}" class="{{ $input }}">{{ old('answer', $faq->answer) }}</textarea>
            </x-field>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="sort_order" :label="__('Display order')" :hint="__('Lower numbers appear first.')">
                    <input id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $faq->sort_order) }}" class="{{ $input }}">
                </x-field>
                <label class="flex cursor-pointer items-start gap-3 pt-7">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq->is_active)) class="mt-0.5 size-4 accent-accent">
                    <span class="text-sm font-medium">{{ __('Show on the website') }}</span>
                </label>
            </div>
        </section>

        <div class="flex flex-col gap-2 sm:flex-row">
            <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-full bg-accent px-6 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                {{ $editing ? __('Save changes') : __('Add question') }}
            </button>
            <a href="{{ route('faqs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-line px-6 text-sm text-muted hover:text-fg">{{ __('Back to FAQ') }}</a>
        </div>
    </form>
</x-layouts.app>
