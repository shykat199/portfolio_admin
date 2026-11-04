@props(['name', 'value' => ''])

@php
    $tools = [
        ['group' => [
            ['action' => 'h1', 'label' => 'Heading 1', 'text' => 'H1'],
            ['action' => 'h2', 'label' => 'Heading 2', 'text' => 'H2'],
            ['action' => 'h3', 'label' => 'Heading 3', 'text' => 'H3'],
            ['action' => 'h4', 'label' => 'Heading 4', 'text' => 'H4'],
        ]],
        ['group' => [
            ['action' => 'bold', 'label' => 'Bold (Ctrl+B)', 'icon' => 'M7 5h6a3.5 3.5 0 0 1 0 7H7zM7 12h7a3.5 3.5 0 0 1 0 7H7z'],
            ['action' => 'italic', 'label' => 'Italic (Ctrl+I)', 'icon' => 'M14 5h-4M14 19h-4M14 5l-4 14'],
            ['action' => 'strike', 'label' => 'Strikethrough (Ctrl+Shift+X)', 'icon' => 'M5 12h14M16 7a4 3 0 0 0-8 0c0 4 8 2 8 6a4 3 0 0 1-8 0'],
            ['action' => 'code', 'label' => 'Inline code (Ctrl+E)', 'icon' => 'm9 8-4 4 4 4M15 8l4 4-4 4'],
        ]],
        ['group' => [
            ['action' => 'link', 'label' => 'Link (Ctrl+K)', 'icon' => 'M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1 1M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1-1'],
            ['action' => 'image', 'label' => 'Upload image', 'icon' => 'M4 5h16v14H4zM4 15l4-4 4 4 3-3 5 5M15 9.5a1 1 0 1 0 0-.01'],
            ['action' => 'codeblock', 'label' => 'Code block', 'icon' => 'M4 4h16v16H4zM9 10l-2 2 2 2M15 10l2 2-2 2'],
            ['action' => 'quote', 'label' => 'Quote', 'icon' => 'M7 10h4v4c0 2-1 3-3 4M15 10h4v4c0 2-1 3-3 4'],
            ['action' => 'tip', 'label' => 'Tip box', 'icon' => 'M9 18h6M10 21h4M12 3a6 6 0 0 0-4 10.5c.7.7 1 1.5 1 2.5h6c0-1 .3-1.8 1-2.5A6 6 0 0 0 12 3Z'],
        ]],
        ['group' => [
            ['action' => 'ul', 'label' => 'Bullet list', 'icon' => 'M9 6h11M9 12h11M9 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01'],
            ['action' => 'ol', 'label' => 'Numbered list', 'icon' => 'M10 6h10M10 12h10M10 18h10M4 5h1v4M4 15h2l-2 3h2'],
            ['action' => 'table', 'label' => 'Table', 'icon' => 'M4 5h16v14H4zM4 10h16M4 15h16M10 5v14'],
            ['action' => 'hr', 'label' => 'Divider', 'icon' => 'M4 12h16'],
        ]],
    ];
@endphp

<div data-md-editor data-mode="write"
    data-preview-url="{{ route('posts.preview') }}"
    data-upload-url="{{ route('posts.images.store') }}"
    class="group/editor overflow-hidden rounded-xl border border-fg/20 bg-surface focus-within:border-accent data-[fullscreen]:fixed data-[fullscreen]:inset-0 data-[fullscreen]:z-50 data-[fullscreen]:flex data-[fullscreen]:flex-col data-[fullscreen]:rounded-none">

    {{-- Toolbar --}}
    <div role="toolbar" aria-label="{{ __('Formatting') }}" class="flex flex-wrap items-center gap-1 border-b border-line bg-raised px-2 py-1.5">
        @foreach ($tools as $tool)
            <div class="flex items-center gap-0.5 border-r border-line pr-1 last:border-r-0">
                @foreach ($tool['group'] as $button)
                    <button type="button" data-md-action="{{ $button['action'] }}" title="{{ __($button['label']) }}" aria-label="{{ __($button['label']) }}"
                        class="flex h-8 min-w-8 cursor-pointer items-center justify-center rounded-md px-1.5 text-muted transition-colors hover:bg-fg/10 hover:text-fg group-data-[mode=preview]/editor:pointer-events-none group-data-[mode=preview]/editor:opacity-40">
                        @isset($button['icon'])
                            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $button['icon'] }}"/></svg>
                        @else
                            <span class="font-mono text-xs font-semibold">{{ $button['text'] }}</span>
                        @endisset
                    </button>
                @endforeach
            </div>
        @endforeach

        <div class="ml-auto flex items-center gap-1">
            <div class="flex rounded-md border border-line p-0.5 text-xs">
                <button type="button" data-md-mode="write" class="cursor-pointer rounded px-2.5 py-1 text-muted group-data-[mode=write]/editor:bg-fg/10 group-data-[mode=write]/editor:text-fg">{{ __('Write') }}</button>
                <button type="button" data-md-mode="split" class="hidden cursor-pointer rounded px-2.5 py-1 text-muted group-data-[mode=split]/editor:bg-fg/10 group-data-[mode=split]/editor:text-fg lg:block">{{ __('Split') }}</button>
                <button type="button" data-md-mode="preview" class="cursor-pointer rounded px-2.5 py-1 text-muted group-data-[mode=preview]/editor:bg-fg/10 group-data-[mode=preview]/editor:text-fg">{{ __('Preview') }}</button>
            </div>
            <button type="button" data-md-fullscreen title="{{ __('Full screen') }}" aria-label="{{ __('Full screen') }}" class="flex size-8 cursor-pointer items-center justify-center rounded-md text-muted hover:bg-fg/10 hover:text-fg">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg>
            </button>
        </div>
    </div>

    {{-- Writing area and preview --}}
    <div class="grid min-h-0 flex-1 group-data-[mode=split]/editor:lg:grid-cols-2">
        <textarea id="{{ $name }}" name="{{ $name }}" data-md-input rows="22" spellcheck="true"
            {{ $attributes->merge(['class' => 'block min-h-[28rem] w-full resize-y bg-transparent px-4 py-3 font-mono text-sm leading-relaxed text-fg placeholder:text-muted/50 focus:outline-none group-data-[fullscreen]/editor:h-full group-data-[fullscreen]/editor:resize-none group-data-[mode=preview]/editor:hidden']) }}
            placeholder="{{ __('Start writing… Use the toolbar or Markdown, e.g. ## Heading, **bold**, - list') }}">{{ $value }}</textarea>
        <div data-md-preview aria-live="polite"
            class="prose-blog hidden max-h-[40rem] min-h-[28rem] overflow-y-auto border-line px-5 py-4 group-data-[fullscreen]/editor:max-h-none group-data-[mode=preview]/editor:block group-data-[mode=split]/editor:lg:block group-data-[mode=split]/editor:lg:border-l"></div>
    </div>

    {{-- Status bar --}}
    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-line bg-raised px-3 py-1.5 text-xs text-muted">
        <span data-md-status>{{ __('Markdown supported. Drop or paste images to upload them.') }}</span>
        <span data-md-count></span>
    </div>

    <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" data-md-file class="hidden">
</div>
