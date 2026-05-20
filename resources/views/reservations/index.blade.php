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
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Gestão</p>
                    <h1 class="text-3xl font-semibold text-[#241b14] dark:text-white">Empréstimos</h1>
                </div>
            </div>
            <div class="mt-5 grid gap-3">
                @foreach ($reservations as $reservation)
                    @php
                        $daysRemaining = $reservation->returned_at ? 0 : now()->startOfDay()->diffInDays($reservation->end_date, false);
                        $isDueSoon = ! $reservation->returned_at && $daysRemaining <= 3;
                        $status = $reservation->returned_at ? 'Devolvido' : ($daysRemaining < 0 ? 'Atrasado' : ucfirst(str_replace('_', ' ', $reservation->status ?? 'ativo')));
                    @endphp
                    <article class="rounded-2xl border {{ $isDueSoon ? 'border-amber-200 bg-amber-50/70 dark:border-amber-900/50 dark:bg-amber-950/20' : 'border-[#eadfce] bg-[#fffaf5] dark:border-[#332820] dark:bg-[#0f0d0b]' }} p-4">
                        <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                            <div>
                                <h2 class="text-lg font-semibold text-[#241b14] dark:text-white">{{ $reservation->resource?->title }}</h2>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold">
                                    <span class="rounded-full bg-white px-3 py-1 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Utilizador: {{ $reservation->user?->name }}</span>
                                    <span class="rounded-full bg-white px-3 py-1 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Entrega: {{ optional($reservation->start_date)->format('d/m/Y') }}</span>
                                    <span class="rounded-full bg-white px-3 py-1 text-[#66594d] ring-1 ring-[#eadfce] dark:bg-[#050505] dark:text-[#cfc5ba] dark:ring-[#332820]">Devolução: {{ optional($reservation->end_date)->format('d/m/Y') }}</span>
                                    <span class="rounded-full px-3 py-1 {{ $daysRemaining < 0 ? 'bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-100' : ($isDueSoon ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-100' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-100') }}">{{ $status }}</span>
                                </div>
                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-white dark:bg-[#050505]">
                                    <span class="block h-full rounded-full {{ $isDueSoon ? 'bg-amber-500' : 'bg-[#FE6807]' }}" style="width: {{ max(8, min(100, 100 - max(0, $daysRemaining) * 10)) }}%"></span>
                                </div>
                            </div>
                            <a class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#FE6807] px-4 text-sm font-bold text-white hover:bg-[#e15f07]" href="{{ route('reservations.show', $reservation->id) }}">Ver detalhes</a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-5 hidden overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase text-[#7f6652] dark:text-[#cfc5ba]">
                        <tr><th class="py-2">Recurso</th><th>Utilizador</th><th>Prazo</th><th>Estado</th><th></th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#eadfce] dark:divide-[#27211a]">
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td class="py-3 font-semibold text-[#241b14] dark:text-white">{{ $reservation->resource?->title }}</td>
                                <td>{{ $reservation->user?->name }}</td>
                                <td>{{ optional($reservation->end_date)->format('Y-m-d') ?? $reservation->end_date }}</td>
                                <td>{{ $reservation->status ?? 'ativo' }}</td>
                                <td><a class="font-semibold text-[#FE6807]" href="{{ route('reservations.show', $reservation->id) }}">Ver</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
