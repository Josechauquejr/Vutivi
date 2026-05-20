@php
    $messages = collect([
        ['type' => 'success', 'message' => session('success')],
        ['type' => 'error', 'message' => session('error')],
        ['type' => 'warning', 'message' => session('warning')],
        ['type' => 'info', 'message' => session('status')],
    ])->filter(fn ($toast) => filled($toast['message']))->values();

    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-100',
        'error' => 'border-red-200 bg-red-50 text-red-900 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-100',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-100',
        'info' => 'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-900/50 dark:bg-sky-950/30 dark:text-sky-100',
    ];
@endphp

@if ($messages->isNotEmpty())
    <div data-toast-region class="fixed right-4 top-24 z-50 grid w-[min(24rem,calc(100vw-2rem))] gap-3">
        @foreach ($messages as $toast)
            <div data-toast class="{{ $styles[$toast['type']] }} flex items-start gap-3 rounded-2xl border p-4 shadow-[0_18px_48px_rgba(54,39,25,0.16)] backdrop-blur-sm">
                <span class="mt-0.5 flex h-8 w-8 flex-none items-center justify-center rounded-xl bg-white/70 dark:bg-black/25">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="{{ $toast['type'] === 'error' ? 'M18 6 6 18M6 6l12 12' : 'M20 6 9 17l-5-5' }}"/>
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-black">{{ $toast['type'] === 'error' ? 'Atenção' : 'Notificação' }}</p>
                    <p class="mt-1 text-sm leading-5">{{ $toast['message'] }}</p>
                </div>
                <button type="button" data-toast-close class="rounded-lg p-1 opacity-70 hover:bg-black/5 hover:opacity-100 dark:hover:bg-white/10">
                    <span class="sr-only">Fechar</span>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
