@php
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
    $file = 'block w-full text-sm text-muted file:mr-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-fg/10 file:px-4 file:py-2 file:text-sm file:text-fg hover:file:bg-fg/15';
    $logo = $settings->url($settings->logo_path);
    $favicon = $settings->url($settings->favicon_path);
    $photo = $settings->url($settings->about_photo_path);
@endphp

<x-layouts.app :title="__('Site settings')">
    <form method="POST" action="{{ route('site-settings.update') }}" enctype="multipart/form-data" novalidate class="grid gap-6 xl:grid-cols-3">
        @csrf
        @method('PUT')

        <div class="space-y-6 xl:col-span-2">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <div>
                    <h2 class="font-medium">{{ __('Hero') }}</h2>
                    <p class="text-xs text-muted">{{ __('The first thing visitors see at the top of the home page.') }}</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-3">
                    <x-field name="hero_prefix" :label="__('Typing line starts with')" class="sm:col-span-1">
                        <input id="hero_prefix" name="hero_prefix" value="{{ old('hero_prefix', $settings->hero_prefix) }}" required maxlength="40" placeholder="{{ __('I build') }}" class="{{ $input }}">
                    </x-field>
                    <div class="sm:col-span-2">
                        <x-list-fields name="hero_words" :label="__('Typed words')" :hint="__('Typed out one after another after the start of the line.')"
                            :items="old('hero_words', $settings->hero_words ?? [])" :placeholder="__('e.g. websites')" :add-label="__('Add word')" />
                    </div>
                </div>
                <x-field name="hero_headline" :label="__('Headline')" :hint="__('The page’s main heading (H1) for Google. Lead with your main search phrase.')">
                    <textarea id="hero_headline" name="hero_headline" rows="2" maxlength="200" required placeholder="{{ __('e.g. Full-stack web developer for fast, SEO-friendly websites that win more customers.') }}" class="{{ $input }} text-lg!">{{ old('hero_headline', $settings->hero_headline) }}</textarea>
                </x-field>
                <x-field name="hero_highlight" :label="__('Coloured words')" :hint="__('Optional. Words from the headline shown in colour; must match exactly.')">
                    <input id="hero_highlight" name="hero_highlight" value="{{ old('hero_highlight', $settings->hero_highlight) }}" maxlength="80" placeholder="{{ __('e.g. more customers') }}" class="{{ $input }}">
                </x-field>
                <x-field name="hero_supporting" :label="__('Text under the headline')">
                    <textarea id="hero_supporting" name="hero_supporting" rows="3" maxlength="400" required placeholder="{{ __('One or two sentences on who you are and how you help.') }}" class="{{ $input }}">{{ old('hero_supporting', $settings->hero_supporting) }}</textarea>
                </x-field>
            </section>

            <section data-seo class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <div>
                    <h2 class="font-medium">{{ __('Home page SEO') }}</h2>
                    <p class="text-xs text-muted">{{ __('How the home page appears in Google and when shared.') }}</p>
                </div>
                <div class="rounded-xl border border-line bg-white p-4 font-sans">
                    <p class="text-[0.7rem] font-medium tracking-wide text-[#5f6368] uppercase">{{ __('Google preview') }}</p>
                    <p class="mt-2 truncate text-xs text-[#202124]">{{ parse_url((string) config('blog.frontend_url'), PHP_URL_HOST) ?: 'your-site.com' }}<span data-seo-preview-slug class="hidden"></span></p>
                    <p data-seo-preview-title class="mt-1 line-clamp-1 text-lg leading-snug text-[#1a0dab]"></p>
                    <p data-seo-preview-description class="mt-1 line-clamp-2 text-sm leading-snug text-[#4d5156]"></p>
                </div>
                <x-field name="seo_title" :label="__('Meta title')">
                    <input id="seo_title" name="seo_title" value="{{ old('seo_title', $settings->seo_title) }}" required maxlength="70" data-seo-input="title" data-seo-limit="60" placeholder="{{ __('e.g. Full-Stack Developer – Laravel, React & Next.js | Your Name') }}" class="{{ $input }}">
                    <p class="mt-1.5 text-xs text-muted"><span data-seo-count="title">0</span> / 60 {{ __('characters recommended') }}</p>
                </x-field>
                <x-field name="seo_description" :label="__('Meta description')">
                    <textarea id="seo_description" name="seo_description" rows="3" maxlength="320" required data-seo-input="description" data-seo-limit="160" placeholder="{{ __('What you do and who for, in one or two sentences.') }}" class="{{ $input }}">{{ old('seo_description', $settings->seo_description) }}</textarea>
                    <p class="mt-1.5 text-xs text-muted"><span data-seo-count="description">0</span> / 160 {{ __('characters recommended') }}</p>
                </x-field>
                <x-field name="seo_keywords" :label="__('Meta keywords')" :hint="__('Separate with commas.')">
                    <input id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords', implode(', ', $settings->seo_keywords ?? [])) }}" placeholder="{{ __('e.g. full-stack web developer, Laravel developer') }}" class="{{ $input }}">
                </x-field>
            </section>

            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <div>
                    <h2 class="font-medium">{{ __('About section') }}</h2>
                    <p class="text-xs text-muted">{{ __('The “About” part of the home page. The photo is also used in the hero and as the author photo on blog posts.') }}</p>
                </div>
                <x-field name="about_title" :label="__('Heading')">
                    <input id="about_title" name="about_title" value="{{ old('about_title', $settings->about_title) }}" required maxlength="120" placeholder="{{ __('e.g. Hello, I’m Shykat') }}" class="{{ $input }}">
                </x-field>
                <x-field name="about_story" :label="__('About text')" :hint="__('Leave an empty line between paragraphs.')">
                    <textarea id="about_story" name="about_story" rows="8" maxlength="4000" required placeholder="{{ __('Introduce yourself: your experience, where you are based and who you work with.') }}" class="{{ $input }}">{{ old('about_story', $settings->about_story) }}</textarea>
                </x-field>
                <x-field name="about_reasons_title" :label="__('List heading')">
                    <input id="about_reasons_title" name="about_reasons_title" value="{{ old('about_reasons_title', $settings->about_reasons_title) }}" required maxlength="120" placeholder="{{ __('e.g. Why you can rely on me') }}" class="{{ $input }}">
                </x-field>
                <x-list-fields name="about_reasons" :label="__('Reasons')" :items="old('about_reasons', $settings->about_reasons ?? [])"
                    :placeholder="__('e.g. You get one fixed price before any work begins')" :add-label="__('Add reason')" :max="8" />
            </section>

            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <div>
                    <h2 class="font-medium">{{ __('Social links') }}</h2>
                    <p class="text-xs text-muted">{{ __('Shown as icons in the footer. Leave a field empty to hide that icon.') }}</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach ([
                        'linkedin_url' => ['LinkedIn', 'https://www.linkedin.com/in/your-name'],
                        'github_url' => ['GitHub', 'https://github.com/your-name'],
                        'facebook_url' => ['Facebook', 'https://www.facebook.com/your-page'],
                        'instagram_url' => ['Instagram', 'https://www.instagram.com/your-name'],
                        'x_url' => ['X (Twitter)', 'https://x.com/your-name'],
                    ] as $field => [$label, $example])
                        <x-field :name="$field" :label="$label">
                            <input id="{{ $field }}" name="{{ $field }}" type="url" value="{{ old($field, $settings->{$field}) }}" maxlength="255" placeholder="{{ $example }}" class="{{ $input }}">
                        </x-field>
                    @endforeach
                    <x-field name="whatsapp_number" :label="__('WhatsApp number')" :hint="__('With country code, e.g. 8801712345678. Also used by the floating WhatsApp button.')">
                        <input id="whatsapp_number" name="whatsapp_number" inputmode="tel" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" maxlength="20" placeholder="8801712345678" class="{{ $input }}">
                    </x-field>
                </div>
            </section>
        </div>

        <div class="space-y-6">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <x-field name="about_photo" :label="__('Your photo')" :hint="__('JPG, PNG or WebP, up to 4 MB, at least 300 × 300.')">
                    <div class="aspect-square overflow-hidden rounded-lg border border-dashed border-line bg-surface">
                        <img data-cover-preview src="{{ $photo }}" alt="" class="size-full object-cover {{ $photo ? '' : 'hidden' }}">
                    </div>
                    <input id="about_photo" name="about_photo" type="file" accept="image/jpeg,image/png,image/webp" data-cover-input class="mt-3 {{ $file }}">
                </x-field>
                <x-field name="about_photo_alt" :label="__('Photo description')">
                    <input id="about_photo_alt" name="about_photo_alt" value="{{ old('about_photo_alt', $settings->about_photo_alt) }}" required maxlength="200" placeholder="{{ __('e.g. Portrait of Shykat Roy') }}" class="{{ $input }}">
                </x-field>
            </section>

            <section class="space-y-6 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <div>
                    <h2 class="font-medium">{{ __('Brand') }}</h2>
                    <p class="text-xs text-muted">{{ __('Leave empty to keep the built-in “SR” badge and icon.') }}</p>
                </div>

                <x-field name="logo" :label="__('Logo')" :hint="__('Shown at the top left of the website. SVG, PNG, JPG or WebP, up to 1 MB. A square logo works best.')">
                    <div class="flex items-center gap-4">
                        <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-full border border-line bg-surface">
                            <img src="{{ $logo }}" alt="" data-logo-preview class="size-10 object-contain {{ $logo ? '' : 'hidden' }}">
                        </span>
                        <input id="logo" name="logo" type="file" accept="image/svg+xml,image/png,image/jpeg,image/webp" data-preview-target="[data-logo-preview]" class="{{ $file }}">
                    </div>
                    @if ($logo)
                        <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm text-muted">
                            <input type="checkbox" name="remove_logo" value="1" class="size-4 accent-accent"> {{ __('Remove and use the built-in badge') }}
                        </label>
                    @endif
                </x-field>

                <x-field name="favicon" :label="__('Favicon')" :hint="__('The small icon in browser tabs. A square PNG, at least 48 × 48 (512 × 512 is ideal), up to 512 KB.')">
                    <div class="flex items-center gap-4">
                        <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-line bg-surface">
                            <img src="{{ $favicon }}" alt="" data-favicon-preview class="size-8 object-contain {{ $favicon ? '' : 'hidden' }}">
                        </span>
                        <input id="favicon" name="favicon" type="file" accept="image/png" data-preview-target="[data-favicon-preview]" class="{{ $file }}">
                    </div>
                    @if ($favicon)
                        <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm text-muted">
                            <input type="checkbox" name="remove_favicon" value="1" class="size-4 accent-accent"> {{ __('Remove and use the built-in icon') }}
                        </label>
                    @endif
                </x-field>
            </section>

            <button type="submit" class="inline-flex min-h-11 w-full cursor-pointer items-center justify-center rounded-full bg-accent px-6 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                {{ __('Save settings') }}
            </button>
        </div>
    </form>
</x-layouts.app>
