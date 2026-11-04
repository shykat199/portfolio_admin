<x-layouts.guest :title="__('Sign in')">
    <div class="rounded-2xl border border-line bg-raised/80 p-6 shadow-[0_30px_80px_-30px_rgb(0_0_0/0.8)] backdrop-blur sm:p-8">
        <h1 class="text-2xl font-semibold">{{ __('Welcome back') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('Sign in to manage your portfolio.') }}</p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5" novalidate>
            @csrf

            <div>
                <label for="email" class="text-sm font-medium">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                    class="mt-2 block w-full rounded-lg border bg-surface px-3.5 py-3 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none @error('email') border-error @else border-fg/20 @enderror"
                    placeholder="you@example.com">
                @error('email')
                    <p id="email-error" class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="text-sm font-medium">{{ __('Password') }}</label>
                <div class="relative mt-2">
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="{{ __('Your password') }}"
                        class="block w-full rounded-lg border border-fg/20 bg-surface py-3 pr-12 pl-3.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none">
                    <button type="button" data-password-toggle="password" aria-label="{{ __('Show password') }}" aria-pressed="false"
                        class="absolute inset-y-0 right-0 flex w-12 cursor-pointer items-center justify-center text-muted hover:text-fg">
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-muted">
                <input type="checkbox" name="remember" value="1" class="size-4 rounded border-fg/30 bg-surface accent-accent" @checked(old('remember'))>
                {{ __('Keep me signed in on this device') }}
            </label>

            <button type="submit"
                class="flex min-h-12 w-full cursor-pointer items-center justify-center rounded-full bg-accent px-6 font-medium text-surface transition-colors hover:bg-accent-strong">
                {{ __('Sign in') }}
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-xs text-muted">{{ __('Protected area. Repeated failed attempts are temporarily blocked.') }}</p>
</x-layouts.guest>
