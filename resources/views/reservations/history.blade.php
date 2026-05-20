<!doctype html>
<html lang="pt">
<x-head title="Histórico de Empréstimos" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Histórico de Empréstimos']]" />

        <section class="mx-auto max-w-6xl rounded-2xl border border-[#eadfce] bg-white p-5 shadow-[0_18px_50px_rgba(54,39,25,0.08)] dark:border-[#27211a] dark:bg-[#090909]">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Histórico</p>
                <h1 class="text-3xl font-semibold text-[#241b14] dark:text-white">Histórico de Empréstimos</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">Consulte empréstimos concluídos, recusados, cancelados ou em atraso.</p>
            </div>

            <form method="GET" action="{{ route('loan-history') }}" class="mt-6 grid gap-3 md:grid-cols-[1fr_220px_auto]">
                <label>
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#7f6652] dark:text-[#cfc5ba]">Pesquisar</span>
                    <input name="q" value="{{ request('q') }}" placeholder="Recurso ou utilizador" class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                </label>
                <label>
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#7f6652] dark:text-[#cfc5ba]">Estado</span>
                    <select name="status" class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                        <option value="">Todos</option>
                        <option value="returned" @selected(request('status') === 'returned')>Devolvidos</option>
                        <option value="denied" @selected(request('status') === 'denied')>Recusados</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelados</option>
                        <option value="overdue" @selected(request('status') === 'overdue')>Atrasados</option>
                    </select>
                </label>
                <button class="mt-5 inline-flex min-h-11 items-center justify-center rounded-lg bg-[#FE6807] px-5 text-sm font-bold text-white hover:bg-[#e15f07] md:mt-6">Filtrar</button>
            </form>

            <div class="mt-6 grid gap-4">
                @forelse ($reservations as $reservation)
                    @php
                        $isOverdue = ! $reservation->returned_at && optional($reservation->end_date)->isPast();
                        $visualStatus = $reservation->returned_at ? 'returned' : ($isOverdue ? 'overdue' : $reservation->status);
                        $labels = ['returned' => 'Devolvido', 'denied' => 'Recusado', 'cancelled' => 'Cancelado', 'overdue' => 'Atrasado'];
                        $badge = match ($visualStatus) {
                            'overdue', 'denied', 'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-100',
                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-100',
                        };
                    @endphp
                    <article class="rounded-2xl border border-[#eadfce] bg-[#fffaf5] p-4 transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_18px_40px_rgba(54,39,25,0.08)] dark:border-[#332820] dark:bg-[#0f0d0b]">
                        <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                            <div>
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h2 class="text-lg font-semibold text-[#241b14] dark:text-white">{{ $reservation->resource?->title ?? 'Recurso removido' }}</h2>
                                        <p class="mt-1 text-sm text-[#66594d] dark:text-[#cfc5ba]">Utilizador: {{ $reservation->user?->name ?? 'Não definido' }}</p>
                                    </div>
                                    <span class="{{ $badge }} rounded-full px-3 py-1 text-xs font-black uppercase tracking-[0.12em]">{{ $labels[$visualStatus] ?? ucfirst((string) $visualStatus) }}</span>
                                </div>
                                <div class="mt-4 grid gap-2 text-xs font-bold sm:grid-cols-3">
                                    <span class="rounded-xl bg-white px-3 py-2 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Pedido: {{ optional($reservation->created_at)->format('d/m/Y') }}</span>
                                    <span class="rounded-xl bg-white px-3 py-2 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Previsto: {{ optional($reservation->end_date)->format('d/m/Y') }}</span>
                                    <span class="rounded-xl bg-white px-3 py-2 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Finalizado: {{ optional($reservation->returned_at)->format('d/m/Y') ?? 'Não finalizado' }}</span>
                                </div>
                            </div>
                            <a href="{{ route('reservations.show', $reservation->id) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#FE6807] px-4 text-sm font-bold text-white hover:bg-[#e15f07]">Ver detalhes</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-[#decbb8] bg-[#fffaf5] p-8 text-center dark:border-[#332820] dark:bg-[#0f0d0b]">
                        <h2 class="text-lg font-semibold text-[#241b14] dark:text-white">Nenhum registo encontrado</h2>
                        <p class="mt-2 text-sm text-[#66594d] dark:text-[#cfc5ba]">Quando houver empréstimos concluídos, recusados ou em atraso, eles aparecerão aqui.</p>
                    </div>
                @endforelse
            </div>

            @if ($reservations->hasPages())
                <div class="mt-5">{{ $reservations->links() }}</div>
            @endif
        </section>
    </main>
</body>
</html>
