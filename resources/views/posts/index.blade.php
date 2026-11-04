<x-layouts.app :title="__('Blog posts')">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-muted">{{ trans_choice(':count post|:count posts', $posts->total()) }}</p>
        <a href="{{ route('posts.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('New post') }}
        </a>
    </div>

    @if ($posts->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-line p-10 text-center">
            <p class="font-medium">{{ __('No posts yet') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('Write your first article to show it on your portfolio blog.') }}</p>
        </div>
    @else
        <ul class="mt-6 divide-y divide-line overflow-hidden rounded-2xl border border-line bg-raised">
            @foreach ($posts as $post)
                <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:p-5">
                    <div class="aspect-[1200/630] w-full shrink-0 overflow-hidden rounded-lg border border-line bg-surface sm:w-36 {{ $post->cover_url ? '' : 'hidden sm:flex sm:items-center sm:justify-center' }}">
                        @if ($post->cover_url)
                            <img src="{{ $post->cover_url }}" alt="" class="size-full object-cover" loading="lazy">
                        @else
                            <span class="text-xs text-muted">{{ __('No cover') }}</span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-status-badge :post="$post" />
                            <span class="text-xs text-muted">{{ $post->category }}</span>
                        </div>
                        <a href="{{ route('posts.edit', $post) }}" class="mt-1.5 block font-medium leading-snug hover:text-accent">{{ $post->title }}</a>
                        <p class="mt-1 font-mono text-xs text-muted">
                            {{ $post->published_at ? __('Publish date: :date', ['date' => $post->published_at->format('j M Y')]) : __('Not published') }}
                            · {{ __('Edited :time', ['time' => $post->updated_at->diffForHumans()]) }}
                        </p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        @if ($post->isLive() && config('blog.frontend_url'))
                            <a href="{{ rtrim(config('blog.frontend_url'), '/') }}/blog/{{ $post->slug }}" target="_blank" rel="noopener" class="inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm text-muted hover:border-fg/30 hover:text-fg">{{ __('View') }}</a>
                        @endif
                        <a href="{{ route('posts.edit', $post) }}" class="inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm hover:border-accent hover:text-accent">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('posts.destroy', $post) }}" data-confirm="{{ __('Delete “:title”? This cannot be undone.', ['title' => $post->title]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg border border-line px-3 text-sm text-muted hover:border-error/50 hover:text-error">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-6">{{ $posts->links() }}</div>
    @endif
</x-layouts.app>
