<x-layouts.app :title="__('Projects')">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-muted">{{ trans_choice(':count project|:count projects', $projects->count()) }} · {{ __('shown in the Work section in this order') }}</p>
        <a href="{{ route('projects.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('New project') }}
        </a>
    </div>

    @if ($projects->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-line p-10 text-center">
            <p class="font-medium">{{ __('No projects yet') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('Add a case study to show your work on the website.') }}</p>
        </div>
    @else
        <ul class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($projects as $project)
                <li class="flex flex-col overflow-hidden rounded-2xl border border-line bg-raised">
                    <a href="{{ route('projects.edit', $project) }}" class="block aspect-[16/10] overflow-hidden border-b border-line bg-surface">
                        <img src="{{ $project->cover_url }}" alt="" loading="lazy" class="size-full object-cover transition-transform duration-500 hover:scale-[1.03]">
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs text-muted">#{{ $project->sort_order }}</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs {{ $project->is_active ? 'border-accent/40 bg-accent/10 text-accent' : 'border-line text-muted' }}">
                                <span class="size-1.5 rounded-full {{ $project->is_active ? 'bg-accent' : 'bg-muted' }}"></span>
                                {{ $project->is_active ? __('Shown') : __('Hidden') }}
                            </span>
                            @if ($project->is_demo)
                                <span class="rounded-full border border-warm/40 px-2.5 py-0.5 text-xs text-warm">{{ __('Demo') }}</span>
                            @endif
                            <span class="ml-auto text-xs text-muted">{{ $project->category }} · {{ $project->year }}</span>
                        </div>
                        <a href="{{ route('projects.edit', $project) }}" class="mt-3 text-lg font-semibold leading-snug hover:text-accent">{{ $project->title }}</a>
                        <p class="mt-1.5 line-clamp-2 text-sm text-muted">{{ $project->problem }}</p>

                        <div class="mt-auto flex flex-wrap items-center gap-2 pt-5">
                            @if ($project->is_active && config('blog.frontend_url'))
                                <a href="{{ rtrim(config('blog.frontend_url'), '/') }}/work/{{ $project->slug }}" target="_blank" rel="noopener" class="inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm text-muted hover:border-fg/30 hover:text-fg">{{ __('View') }}</a>
                            @endif
                            <a href="{{ route('projects.edit', $project) }}" class="inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm hover:border-accent hover:text-accent">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="ml-auto" data-confirm="{{ __('Delete “:title” and its images? This cannot be undone.', ['title' => $project->title]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg border border-line px-3 text-sm text-muted hover:border-error/50 hover:text-error">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.app>
