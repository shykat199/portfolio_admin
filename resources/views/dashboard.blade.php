<x-layouts.app :title="__('Dashboard')">
    <section class="rounded-2xl border border-line bg-raised p-6 sm:p-8">
        <p class="font-mono text-xs text-accent">{{ now()->format('l, j F Y') }}</p>
        <h2 class="mt-2 text-2xl font-semibold sm:text-3xl">{{ __('Welcome back, :name', ['name' => $user->name]) }}</h2>
        <p class="mt-2 max-w-2xl text-muted">{{ __('This is where you will manage your portfolio content. More sections are on the way.') }}</p>
    </section>

    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('posts.index') }}" class="group rounded-2xl border border-line bg-raised p-6 transition-colors hover:border-accent/40">
            <p class="text-sm text-muted">{{ __('Published posts') }}</p>
            <p class="mt-2 text-4xl font-semibold text-accent">{{ $publishedCount }}</p>
            <p class="mt-1 text-sm text-muted group-hover:text-fg">{{ __('Manage blog posts →') }}</p>
        </a>
        <a href="{{ route('posts.index') }}" class="group rounded-2xl border border-line bg-raised p-6 transition-colors hover:border-accent/40">
            <p class="text-sm text-muted">{{ __('Drafts') }}</p>
            <p class="mt-2 text-4xl font-semibold">{{ $draftCount }}</p>
            <p class="mt-1 text-sm text-muted group-hover:text-fg">{{ __('Finish and publish →') }}</p>
        </a>
        <a href="{{ route('posts.create') }}" class="group flex flex-col justify-center rounded-2xl border border-dashed border-accent/40 bg-accent/[0.05] p-6 transition-colors hover:bg-accent/10 sm:col-span-2 lg:col-span-1">
            <p class="text-lg font-semibold text-accent">{{ __('+ Write a new post') }}</p>
            <p class="mt-1 text-sm text-muted">{{ __('Share a guide or update with your readers.') }}</p>
        </a>
    </div>

    @if ($latestPosts->isNotEmpty())
        <section class="mt-6 rounded-2xl border border-line bg-raised p-6">
            <h2 class="font-semibold">{{ __('Recently edited') }}</h2>
            <ul class="mt-4 divide-y divide-line">
                @foreach ($latestPosts as $post)
                    <li class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                        <a href="{{ route('posts.edit', $post) }}" class="min-w-0 truncate hover:text-accent">{{ $post->title }}</a>
                        <div class="flex shrink-0 items-center gap-3">
                            <x-status-badge :post="$post" />
                            <span class="text-xs text-muted">{{ $post->updated_at->diffForHumans() }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="rounded-2xl border border-line bg-raised p-6 lg:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-semibold">{{ __('Latest messages') }}</h2>
                <a href="{{ route('messages.index') }}" class="text-sm text-accent hover:underline">{{ __('Open inbox →') }}</a>
            </div>
            @if ($latestMessages->isEmpty())
                <p class="mt-4 text-sm text-muted">{{ __('No messages yet. Enquiries from your contact form will appear here.') }}</p>
            @else
                <ul class="mt-3 divide-y divide-line">
                    @foreach ($latestMessages as $message)
                        <li>
                            <a href="{{ route('messages.show', $message) }}" class="flex items-start gap-3 py-3 hover:text-accent">
                                <span class="mt-2 size-2 shrink-0 rounded-full {{ $message->isUnread() ? 'bg-accent' : 'bg-transparent' }}" aria-hidden="true"></span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate {{ $message->isUnread() ? 'font-semibold' : '' }}">{{ $message->name }} <span class="font-normal text-muted">· {{ $message->business }}</span></span>
                                    <span class="block truncate text-sm text-muted">{{ $message->message }}</span>
                                </span>
                                <span class="shrink-0 font-mono text-xs text-muted">{{ $message->created_at->diffForHumans(short: true) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-2xl border border-line bg-raised p-6">
            <h2 class="font-semibold">{{ __('Account security') }}</h2>
            <dl class="mt-4 space-y-4 text-sm">
                <div>
                    <dt class="text-muted">{{ __('Signed in as') }}</dt>
                    <dd class="mt-0.5 break-all">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-muted">{{ __('Last sign-in') }}</dt>
                    <dd class="mt-0.5">{{ $user->last_login_at?->format('j M Y, H:i') ?? __('First sign-in') }}</dd>
                </div>
                <div>
                    <dt class="text-muted">{{ __('From IP address') }}</dt>
                    <dd class="mt-0.5 font-mono">{{ $user->last_login_ip ?? '—' }}</dd>
                </div>
            </dl>
            <a href="{{ route('settings.password.edit') }}" class="mt-6 inline-flex min-h-10 items-center rounded-full border border-fg/20 px-4 text-sm transition-colors hover:border-accent hover:text-accent">
                {{ __('Change password') }}
            </a>
        </section>
    </div>
</x-layouts.app>
