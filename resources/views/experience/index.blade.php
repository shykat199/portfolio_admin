<x-layouts.app :title="__('Experience')">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-muted">{{ trans_choice(':count job|:count jobs', $jobs->count()) }} · {{ __('shown on the website in this order, newest first') }}</p>
        <a href="{{ route('experience.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 text-sm font-medium text-surface transition-colors hover:bg-accent-strong">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('Add job') }}
        </a>
    </div>

    @if ($jobs->isEmpty())
        <div class="mt-6 rounded-2xl border border-dashed border-line p-10 text-center">
            <p class="font-medium">{{ __('No jobs yet') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('The Experience section stays hidden on the website until you add one.') }}</p>
        </div>
    @else
        <ol class="relative mt-6 space-y-4 border-l border-line pl-6 sm:ml-2">
            @foreach ($jobs as $job)
                <li class="relative rounded-2xl border bg-raised p-5 sm:p-6 {{ $job->isCurrent() ? 'border-accent/40' : 'border-line' }} {{ $job->is_active ? '' : 'opacity-60' }}">
                    <span aria-hidden="true" class="absolute top-7 -left-[30px] size-[11px] rounded-full {{ $job->isCurrent() ? 'bg-accent' : 'border border-fg/40 bg-surface' }}"></span>
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <a href="{{ route('experience.edit', $job) }}" class="text-lg font-semibold hover:text-accent">{{ $job->role }}</a>
                            <p class="text-sm text-muted">{{ $job->company }} · {{ $job->type }}{{ $job->location ? ' · '.$job->location : '' }}</p>
                        </div>
                        <div class="flex items-center gap-2 font-mono text-xs text-muted">
                            @if ($job->isCurrent())
                                <span class="rounded-full bg-accent/15 px-2.5 py-1 text-accent">{{ __('Now') }}</span>
                            @endif
                            {{ $job->start }} – {{ $job->end ?? __('Present') }}
                        </div>
                    </div>
                    <p class="mt-3 line-clamp-2 text-sm text-muted">{{ $job->summary }}</p>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-xs text-muted">#{{ $job->sort_order }} · {{ $job->is_active ? __('Shown') : __('Hidden') }}</span>
                        <a href="{{ route('experience.edit', $job) }}" class="ml-auto inline-flex min-h-9 items-center rounded-lg border border-line px-3 text-sm hover:border-accent hover:text-accent">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('experience.destroy', $job) }}" data-confirm="{{ __('Remove :role at :company?', ['role' => $job->role, 'company' => $job->company]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg border border-line px-3 text-sm text-muted hover:border-error/50 hover:text-error">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
</x-layouts.app>
