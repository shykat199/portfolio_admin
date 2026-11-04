@props(['title'])

@php
    $icons = [
        'dashboard' => 'M4 4h7v7H4zM13 4h7v4h-7zM13 10h7v10h-7zM4 13h7v7H4z',
        'password' => 'M7 11V8a5 5 0 0 1 10 0v3M5 11h14v10H5zM12 15v2',
        'settings' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z',
        'posts' => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14ZM8 7h8M8 11h6',
        'services' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'projects' => 'M3 8h18v12H3zM8 8V5h8v3M3 13h18',
        'tech' => 'm8 9-4 3 4 3M16 9l4 3-4 3M13.5 6l-3 12',
        'experience' => 'M12 15a6 6 0 1 0 0-12 6 6 0 0 0 0 12ZM8.5 14 7 21l5-3 5 3-1.5-7',
        'testimonials' => 'M7 10h4v4c0 2-1 3-3 4M15 10h4v4c0 2-1 3-3 4M7 6h4M15 6h4',
        'faqs' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18ZM9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6M12 17h.01',
        'messages' => 'M4 5h16v12H8l-4 3V5ZM8 9h8M8 13h5',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-fg focus:px-4 focus:py-2 focus:text-surface">{{ __('Skip to content') }}</a>

    <div data-sidebar-backdrop class="fixed inset-0 z-30 hidden bg-black/60 backdrop-blur-sm lg:hidden"></div>

    {{-- Sidebar: a slide-in drawer on phones and tablets, always visible on large screens. --}}
    <aside data-sidebar data-open="false" id="sidebar"
        class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-line bg-raised transition-transform duration-300 data-[open=true]:translate-x-0 lg:translate-x-0">
        <div class="flex h-16 items-center justify-between border-b border-line px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-full bg-accent font-mono text-xs font-semibold text-surface">SR</span>
                <span class="font-medium">{{ config('app.name') }}</span>
            </a>
            <button type="button" data-sidebar-toggle aria-controls="sidebar" aria-label="{{ __('Close menu') }}"
                class="flex size-9 cursor-pointer items-center justify-center rounded-lg text-muted hover:bg-fg/5 hover:text-fg lg:hidden">
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>

        <nav aria-label="{{ __('Main') }}" class="flex-1 space-y-6 overflow-y-auto p-4">
            <div class="space-y-1">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" :icon="$icons['dashboard']">{{ __('Dashboard') }}</x-nav-link>
                <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')" :icon="$icons['messages']">
                    {{ __('Messages') }}
                    @if ($unreadMessages > 0)
                        <span class="float-right rounded-full bg-accent px-2 py-0.5 text-[0.7rem] font-semibold text-surface">{{ $unreadMessages }}<span class="sr-only"> {{ __('unread') }}</span></span>
                    @endif
                </x-nav-link>
            </div>

            <div>
                <p class="px-3 font-mono text-[0.7rem] tracking-wide text-muted/70 uppercase">{{ __('Content') }}</p>
                <div class="mt-2 space-y-1">
                    <x-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')" :icon="$icons['posts']">{{ __('Blog posts') }}</x-nav-link>
                    <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')" :icon="$icons['services']">{{ __('Services') }}</x-nav-link>
                    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')" :icon="$icons['projects']">{{ __('Projects') }}</x-nav-link>
                    <x-nav-link :href="route('tech.index')" :active="request()->routeIs('tech.*')" :icon="$icons['tech']">{{ __('Tech stack') }}</x-nav-link>
                    <x-nav-link :href="route('experience.index')" :active="request()->routeIs('experience.*')" :icon="$icons['experience']">{{ __('Experience') }}</x-nav-link>
                </div>
                <div class="mt-1 space-y-1">
                    <x-nav-link :href="route('testimonials.index')" :active="request()->routeIs('testimonials.*')" :icon="$icons['testimonials']">{{ __('Testimonials') }}</x-nav-link>
                    <x-nav-link :href="route('faqs.index')" :active="request()->routeIs('faqs.*')" :icon="$icons['faqs']">{{ __('FAQ') }}</x-nav-link>
                </div>
            </div>

            <div>
                <p class="px-3 font-mono text-[0.7rem] tracking-wide text-muted/70 uppercase">{{ __('Account') }}</p>
                <div class="mt-2 space-y-1">
                    <x-nav-link :href="route('site-settings.edit')" :active="request()->routeIs('site-settings.*')" :icon="$icons['settings']">{{ __('Site settings') }}</x-nav-link>
                    <x-nav-link :href="route('settings.password.edit')" :active="request()->routeIs('settings.password.*')" :icon="$icons['password']">{{ __('Change password') }}</x-nav-link>
                </div>
            </div>
        </nav>

        <div class="border-t border-line p-4">
            <div class="flex items-center gap-3 px-1">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-accent/15 font-mono text-xs font-semibold text-accent">
                    {{ auth()->user()->initials() }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg border border-line px-3 py-2.5 text-sm text-muted transition-colors hover:border-error/50 hover:text-error">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h10"/></svg>
                    {{ __('Sign out') }}
                </button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-surface/85 px-4 backdrop-blur sm:px-6 lg:px-8">
            <button type="button" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false" aria-label="{{ __('Open menu') }}"
                class="flex size-10 cursor-pointer items-center justify-center rounded-lg border border-line text-fg hover:bg-fg/5 lg:hidden">
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <h1 class="truncate text-lg font-semibold">{{ $title }}</h1>
        </header>

        <main id="main" class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            {{ $slot }}
        </main>
    </div>
    <x-toasts />
</body>
</html>
