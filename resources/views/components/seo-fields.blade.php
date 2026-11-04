@props([
    'path', 'slug' => null, 'title' => null, 'description' => null, 'keywords' => null,
    'hint' => '', 'titlePlaceholder' => '', 'descriptionPlaceholder' => '', 'keywordsHint' => '',
])

@php
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
@endphp

<section data-seo class="rounded-2xl border border-line bg-raised p-5 sm:p-6">
    <h2 class="font-medium">{{ __('Search engine (SEO)') }}</h2>
    <p class="text-xs text-muted">{{ $hint }}</p>

    <div class="mt-5 rounded-xl border border-line bg-white p-4 font-sans">
        <p class="text-[0.7rem] font-medium tracking-wide text-[#5f6368] uppercase">{{ __('Google preview') }}</p>
        <p class="mt-2 truncate text-xs text-[#202124]">
            {{ parse_url((string) config('blog.frontend_url'), PHP_URL_HOST) ?: 'your-site.com' }} › {{ $path }} › <span data-seo-preview-slug>{{ $slug ?: 'your-page' }}</span>
        </p>
        <p data-seo-preview-title class="mt-1 line-clamp-1 text-lg leading-snug text-[#1a0dab]"></p>
        <p data-seo-preview-description class="mt-1 line-clamp-2 text-sm leading-snug text-[#4d5156]"></p>
    </div>

    <div class="mt-5 space-y-5">
        <x-field name="seo_title" :label="__('Meta title')">
            <input id="seo_title" name="seo_title" value="{{ $title }}" maxlength="70" data-seo-input="title" data-seo-limit="60" placeholder="{{ $titlePlaceholder }}" class="{{ $input }}">
            <p class="mt-1.5 text-xs text-muted"><span data-seo-count="title">0</span> / 60 {{ __('characters recommended') }}</p>
        </x-field>
        <x-field name="meta_description" :label="__('Meta description')">
            <textarea id="meta_description" name="meta_description" rows="3" maxlength="320" data-seo-input="description" data-seo-limit="160" placeholder="{{ $descriptionPlaceholder }}" class="{{ $input }}">{{ $description }}</textarea>
            <p class="mt-1.5 text-xs text-muted"><span data-seo-count="description">0</span> / 160 {{ __('characters recommended') }}</p>
        </x-field>
        <x-field name="meta_keywords" :label="__('Meta keywords')" :hint="$keywordsHint">
            <input id="meta_keywords" name="meta_keywords" value="{{ $keywords }}" placeholder="{{ __('e.g. website cost, small business website') }}" class="{{ $input }}">
        </x-field>
    </div>
</section>
