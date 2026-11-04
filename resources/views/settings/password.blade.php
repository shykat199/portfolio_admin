<x-layouts.app :title="__('Change password')">
    <section class="max-w-xl rounded-2xl border border-line bg-raised p-6 sm:p-8">
        <h2 class="text-xl font-semibold">{{ __('Update your password') }}</h2>
        <p class="mt-1 text-sm text-muted">{{ __('Use at least 12 characters with upper and lower case letters, a number and a symbol. Other devices will be signed out.') }}</p>

        <form method="POST" action="{{ route('settings.password.update') }}" class="mt-6 space-y-5" novalidate>
            @csrf
            @method('PUT')

            @foreach ([
                ['name' => 'current_password', 'label' => __('Current password'), 'autocomplete' => 'current-password', 'placeholder' => __('Enter your current password')],
                ['name' => 'password', 'label' => __('New password'), 'autocomplete' => 'new-password', 'placeholder' => __('At least 12 characters')],
                ['name' => 'password_confirmation', 'label' => __('Confirm new password'), 'autocomplete' => 'new-password', 'placeholder' => __('Type the new password again')],
            ] as $field)
                <div>
                    <label for="{{ $field['name'] }}" class="text-sm font-medium">{{ $field['label'] }}</label>
                    <div class="relative mt-2">
                        <input id="{{ $field['name'] }}" name="{{ $field['name'] }}" type="password" required autocomplete="{{ $field['autocomplete'] }}" placeholder="{{ $field['placeholder'] }}"
                            @error($field['name']) aria-invalid="true" aria-describedby="{{ $field['name'] }}-error" @enderror
                            class="block w-full rounded-lg border bg-surface py-3 pr-12 pl-3.5 text-base placeholder:text-muted/50 focus:border-accent focus:outline-none @error($field['name']) border-error @else border-fg/20 @enderror">
                        <button type="button" data-password-toggle="{{ $field['name'] }}" aria-label="{{ __('Show password') }}" aria-pressed="false"
                            class="absolute inset-y-0 right-0 flex w-12 cursor-pointer items-center justify-center text-muted hover:text-fg">
                            <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error($field['name'])
                        <p id="{{ $field['name'] }}-error" class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-full bg-accent px-6 font-medium text-surface transition-colors hover:bg-accent-strong">
                {{ __('Save new password') }}
            </button>
        </form>
    </section>
</x-layouts.app>
