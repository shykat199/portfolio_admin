@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans">
    <div aria-hidden="true" class="auth-glow fixed inset-0"></div>
    <div aria-hidden="true" class="auth-grid fixed inset-0 opacity-50"></div>

    <main class="relative flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-8 flex items-center justify-center gap-3">
                <span class="flex size-11 items-center justify-center rounded-full bg-accent font-mono text-sm font-semibold text-surface">SR</span>
                <span class="text-lg font-medium">{{ config('app.name') }}</span>
            </div>

            {{ $slot }}
        </div>
    </main>
    <x-toasts />
</body>
</html>
