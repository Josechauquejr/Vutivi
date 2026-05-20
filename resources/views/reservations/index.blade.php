<!doctype html>
<html lang="pt">
<x-head title="Empréstimos" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Empréstimos']]" />
        <section class="mx-auto max-w-6xl rounded-2xl border border-[#eadfce] bg-white p-5 shadow-[0_18px_50px_rgba(54,39,25,0.08)] dark:border-[#27211a] dark:bg-[#090909]">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Reservas e empréstimos</p>
                    <h1 class="text-3xl font-semibold text-[#241b14] dark:text-white">Acompanhamento</h1>
                    <p class="mt-2 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">Consulte pedidos, prazos, atrasos e devoluções com estado claro.</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4">
                @forelse ($reservations as $reservation)
                    @php
                        $daysRemaining = $reservation->returned_at ? 0 : now()->startOfDay()->diffInDays($reservation->end_date, false);
                        $isOverdue = ! $reservation->returned_at && $daysRemaining < 0;
                        $isDueSoon = ! $reservation->returned_at && ! $isOverdue && $daysRemaining <= 3;
                        $visualStatus = $reservation->returned_at ? 'returned' : ($isOverdue ? 'overdue' : $reservation->status);
                        $labels = [
                            'pending' => 'Pendente',
                            'approved' => 'Aprovado',
                            'in_use' => 'Em uso',
                            'extension_pending' => 'Extensão solicitada',
                            'extended' => 'Prazo estendido',
                            'returned' => 'Devolvido',
                            'cancelled' => 'Cancelado',
                            'denied' => 'Recusado',
                            'overdue' => 'Atrasado',
                        ];
                        $badge = match ($visualStatus) {
                            'pending', 'extension_pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-100',
                            'overdue', 'denied', 'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-100',
                            'returned' => 'bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-100',
                            default => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-100',
                        };
                        $progress = $reservation->returned_at ? 100 : ($isOverdue ? 100 : max(12, min(100, 100 - max(0, $daysRemaining) * 8)));
                    @endphp
                    <article class="rounded-2xl border {{ $isDueSoon ? 'border-amber-200 bg-amber-50/70 dark:border-amber-900/50 dark:bg-amber-950/20' : 'border-[#eadfce] bg-[#fffaf5] dark:border-[#332820] dark:bg-[#0f0d0b]' }} p-4 transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_40px_rgba(54,39,25,0.08)]">
                        <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h2 class="break-words text-lg font-semibold text-[#241b14] dark:text-white">{{ $reservation->resource?->title ?? 'Recurso removido' }}</h2>
                                        <p class="mt-1 text-sm text-[#66594d] dark:text-[#cfc5ba]">Utilizador: {{ $reservation->user?->name ?? 'Não definido' }}</p>
                                    </div>
                                    <span class="{{ $badge }} rounded-full px-3 py-1 text-xs font-black uppercase tracking-[0.12em]">{{ $labels[$visualStatus] ?? 'Ativo' }}</span>
                                </div>

                                <div class="mt-4 grid gap-2 text-xs font-bold sm:grid-cols-3">
                                    <span class="rounded-xl bg-white px-3 py-2 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Pedido: {{ optional($reservation->created_at)->format('d/m/Y') }}</span>
                                    <span class="rounded-xl bg-white px-3 py-2 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Entrega: {{ optional($reservation->picked_up_at ?? $reservation->start_date)->format('d/m/Y') ?? 'Pendente' }}</span>
                                    <span class="rounded-xl bg-white px-3 py-2 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Devolução: {{ optional($reservation->end_date)->format('d/m/Y') }}</span>
                                </div>

                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-white dark:bg-[#050505]">
                                    <span class="block h-full rounded-full {{ $isOverdue ? 'bg-red-500' : ($isDueSoon ? 'bg-amber-500' : 'bg-[#FE6807]') }}" style="width: {{ $progress }}%"></span>
                                </div>
                                <p class="mt-2 text-xs font-semibold text-[#7f6652] dark:text-[#cfc5ba]">
                                    {{ $reservation->returned_at ? 'Empréstimo finalizado.' : ($isOverdue ? abs($daysRemaining) . ' dias em atraso.' : $daysRemaining . ' dias restantes.') }}
                                </p>
                            </div>

                            <a class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#FE6807] px-4 text-sm font-bold text-white hover:bg-[#e15f07]" href="{{ route('reservations.show', $reservation->id) }}">
                                {{ $reservation->status === \App\Models\Reservation::STATUS_PENDING ? 'Detalhes da reserva' : 'Ver detalhes do empréstimo' }}
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-[#decbb8] bg-[#fffaf5] p-8 text-center dark:border-[#332820] dark:bg-[#0f0d0b]">
                        <h2 class="text-lg font-semibold text-[#241b14] dark:text-white">Nenhum empréstimo encontrado</h2>
                        <p class="mt-2 text-sm text-[#66594d] dark:text-[#cfc5ba]">As suas reservas e empréstimos aparecerão aqui.</p>
                    </div>
                @endforelse
            </div>

            @if ($reservations->hasPages())
                <div class="mt-5 text-sm font-semibold text-[#66594d] dark:text-[#cfc5ba]">
                    Página {{ $reservations->currentPage() }} de {{ $reservations->lastPage() }}
                </div>
            @endif
        </section>
    </main>
</body>
</html>
