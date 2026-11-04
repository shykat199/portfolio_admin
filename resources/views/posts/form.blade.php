@php
    $editing = $post->exists;
    $input = 'block w-full rounded-lg border border-fg/20 bg-surface px-3.5 py-2.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none sm:text-sm';
    $faq = old('faq', $post->faq ?: []);
@endphp

<x-layouts.app :title="$editing ? __('Edit post') : __('New post')">
    <form method="POST" action="{{ $editing ? route('posts.update', $post) : route('posts.store') }}" enctype="multipart/form-data" novalidate class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="space-y-6 xl:col-span-2">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <x-field name="title" :label="__('Title')">
                    <input id="title" name="title" value="{{ old('title', $post->title) }}" required maxlength="160" placeholder="{{ __('e.g. How much does a small business website cost?') }}" data-slug-source data-seo-fallback="title" class="{{ $input }} text-lg! font-medium">
                </x-field>
                <x-field name="slug" :label="__('Web address')" :hint="$editing ? __('Changing this breaks existing links to the post.') : __('Created from the title as you type. You can change it.')">
                    <div class="flex items-center overflow-hidden rounded-lg border border-fg/20 bg-surface focus-within:border-accent">
                        <span class="pl-3.5 font-mono text-xs text-muted">/blog/</span>
                        <input id="slug" name="slug" value="{{ old('slug', $post->slug) }}" maxlength="160" data-slug-target data-auto="{{ $editing || old('slug') ? 'false' : 'true' }}" class="w-full bg-transparent px-1 py-2.5 font-mono text-sm focus:outline-none" placeholder="my-article">
                    </div>
                </x-field>
                <x-field name="description" :label="__('Summary')" :hint="__('Shown on the blog list and in Google. Aim for 120 to 160 characters.')">
                    <textarea id="description" name="description" rows="3" maxlength="300" required placeholder="{{ __('A short summary of what readers will learn, in one or two sentences.') }}" data-seo-fallback="description" class="{{ $input }}">{{ old('description', $post->description) }}</textarea>
                </x-field>
            </section>

            <section class="rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <x-field name="body" :label="__('Article')" :hint="__('H1 and H2 headings are listed in “On this page” on the website.')">
                    <x-markdown-editor name="body" :value="old('body', $post->body)" />
                </x-field>
            </section>

            <x-faq-fields :items="$faq" :hint="__('Shown at the end of the article and sent to Google. Empty rows are ignored.')" />

            <x-seo-fields
                path="blog"
                :slug="$post->slug"
                :title="old('seo_title', $post->seo_title)"
                :description="old('meta_description', $post->meta_description)"
                :keywords="old('meta_keywords', implode(', ', $post->meta_keywords ?? []))"
                :hint="__('How the post appears in Google and when shared. Empty fields use the title, summary and topics.')"
                :title-placeholder="__('Uses the post title if empty')"
                :description-placeholder="__('Uses the summary if empty')"
                :keywords-hint="__('Separate with commas. Uses the topics if empty.')" />
        </div>

        <div class="space-y-6">
            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <x-field name="status" :label="__('Status')">
                    <select id="status" name="status" class="{{ $input }}">
                        @foreach (\App\PostStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $post->status?->value) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field name="published_at" :label="__('Publish date')" :hint="__('Leave empty to use today when publishing. A future date schedules the post.')">
                    <input id="published_at" name="published_at" type="date" value="{{ old('published_at', $post->published_at?->toDateString()) }}" class="{{ $input }}">
                </x-field>

                <div class="flex flex-col gap-2 border-t border-line pt-5 sm:flex-row xl:flex-col">
                    <button type="submit" class="inline-flex min-h-11 flex-1 cursor-pointer items-center justify-center rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
                        {{ $editing ? __('Save changes') : __('Create post') }}
                    </button>
                    <a href="{{ route('posts.index') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-full border border-line px-5 text-sm text-muted hover:text-fg">{{ __('Back to posts') }}</a>
                </div>
                @if ($editing && $post->isLive() && config('blog.frontend_url'))
                    <a href="{{ rtrim(config('blog.frontend_url'), '/') }}/blog/{{ $post->slug }}" target="_blank" rel="noopener" class="block text-center text-sm text-accent hover:underline">{{ __('View on website ↗') }}</a>
                @endif
            </section>

            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <x-field name="cover" :label="__('Cover image')" :hint="__('JPG, PNG or WebP, up to 4 MB. Best at 1200 × 630.')">
                    <div class="aspect-[1200/630] overflow-hidden rounded-lg border border-dashed border-line bg-surface">
                        <img data-cover-preview src="{{ $post->cover_url }}" alt="" class="size-full object-cover {{ $post->cover_url ? '' : 'hidden' }}">
                    </div>
                    <input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp" data-cover-input
                        class="mt-3 block w-full text-sm text-muted file:mr-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-fg/10 file:px-4 file:py-2 file:text-sm file:text-fg hover:file:bg-fg/15">
                </x-field>
                <x-field name="cover_alt" :label="__('Image description')" :hint="__('Describe the image for screen readers and Google.')">
                    <input id="cover_alt" name="cover_alt" value="{{ old('cover_alt', $post->cover_alt) }}" maxlength="200" placeholder="{{ __('e.g. A laptop showing a booking calendar') }}" class="{{ $input }}">
                </x-field>
            </section>

            <section class="space-y-5 rounded-2xl border border-line bg-raised p-5 sm:p-6">
                <x-field name="category" :label="__('Category')">
                    <input id="category" name="category" value="{{ old('category', $post->category) }}" required maxlength="60" placeholder="{{ __('e.g. Planning') }}" class="{{ $input }}">
                </x-field>
                <x-field name="tags" :label="__('Topics')" :hint="__('Separate with commas.')">
                    <input id="tags" name="tags" value="{{ old('tags', implode(', ', $post->tags ?? [])) }}" placeholder="{{ __('Websites, Pricing') }}" class="{{ $input }}">
                </x-field>
                <x-field name="service" :label="__('Related service')">
                    <select id="service" name="service" class="{{ $input }}">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($services as $slug => $label)
                            <option value="{{ $slug }}" @selected(old('service', $post->service) === $slug)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
            </section>
        </div>
    </form>

    @if ($editing)
        <form method="POST" action="{{ route('posts.destroy', $post) }}" class="mt-6" data-confirm="{{ __('Delete this post? This cannot be undone.') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-sm text-muted hover:text-error">{{ __('Delete this post') }}</button>
        </form>
    @endif
</x-layouts.app>
