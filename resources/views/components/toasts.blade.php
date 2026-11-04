{{-- Pop-up messages after an action. They slide in, then fade away on their own. --}}
@php
    $toasts = [];
    if (session('toast')) {
        $toasts[] = session('toast');
    }
    if ($errors->any()) {
        $toasts[] = ['type' => 'error', 'message' => $errors->count() === 1 ? $errors->first() : __('Please fix the :count highlighted fields.', ['count' => $errors->count()])];
    }
@endphp

<div aria-live="polite" class="pointer-events-none fixed inset-x-4 top-4 z-50 flex flex-col items-center gap-3 sm:inset-x-auto sm:right-6 sm:items-end">
    @foreach ($toasts as $toast)
        @php($isError = ($toast['type'] ?? 'success') === 'error')
        <div data-toast role="{{ $isError ? 'alert' : 'status' }}"
            class="pointer-events-auto flex w-full max-w-sm translate-y-[-8px] items-start gap-3 rounded-xl border bg-raised/95 px-4 py-3 opacity-0 shadow-[0_18px_40px_-12px_rgb(0_0_0/0.8)] backdrop-blur transition duration-300 ease-out data-[shown]:translate-y-0 data-[shown]:opacity-100 {{ $isError ? 'border-error/40' : 'border-accent/40' }}">
            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full {{ $isError ? 'bg-error/15 text-error' : 'bg-accent/15 text-accent' }}">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="{{ $isError ? 'M12 8v5M12 16.5v.5' : 'm5 12 5 5 9-10' }}"/>
                </svg>
            </span>
            <p class="flex-1 text-sm leading-relaxed">{{ $toast['message'] }}</p>
            <button type="button" data-toast-close aria-label="{{ __('Dismiss') }}" class="-mr-1 flex size-7 shrink-0 cursor-pointer items-center justify-center rounded-lg text-muted hover:bg-fg/5 hover:text-fg">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
    @endforeach
</div>
