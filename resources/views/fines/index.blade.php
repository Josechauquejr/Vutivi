<!doctype html>
<html lang="pt">
<x-head title="Multas" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <section class="mx-auto max-w-4xl space-y-5">
            <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-red-500">Multas</p>
                <h1 class="mt-1 text-3xl font-semibold text-[#241b14] dark:text-white">As suas multas</h1>
                @if ($unpaidTotal > 0)
                    <p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-400">Total em dívida: {{ number_format($unpaidTotal, 2) }} €</p>
                @else
                    <p class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">Sem multas pendentes.</p>
                @endif
            </div>

            @forelse ($fines as $fine)
                @php
                    $paid = $fine->isPaid();
                @endphp
                <div class="rounded-2xl border {{ $paid ? 'border-[#eadfce]' : 'border-red-200 dark:border-red-900/40' }} bg-white p-4 dark:bg-[#090909]">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-[#241b14] dark:text-white">
                                {{ $fine->reservation?->resource?->title ?? 'Recurso removido' }}
                            </p>
                            <p class="mt-0.5 text-sm text-[#66594d] dark:text-[#cfc5ba]">
                                {{ $fine->days_overdue }} {{ $fine->days_overdue === 1 ? 'dia' : 'dias' }} em atraso × {{ number_format($fine->rate_per_day, 2) }} €/dia
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold {{ $paid ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($fine->total, 2) }} €
                            </span>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $paid ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300' }}">
                                {{ $paid ? 'Pago' : 'Pendente' }}
                            </span>
                        </div>
                    </div>
                    @if ($fine->paid_at)
                        <p class="mt-1 text-xs text-[#9b8b7c]">Pago em {{ $fine->paid_at->format('d/m/Y') }}</p>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-[#eadfce] bg-white p-8 text-center dark:border-[#27211a] dark:bg-[#090909]">
                    <p class="text-[#66594d] dark:text-[#cfc5ba]">Não tem multas registadas.</p>
                </div>
            @endforelse

            <div>{{ $fines->links() }}</div>
        </section>
    </main>
</body>
</html>
