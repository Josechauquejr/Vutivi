<!doctype html>
<html lang="pt">
<x-head title="Painel do Bibliotecário" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Painel do Bibliotecário']]" />
        <section class="mx-auto max-w-5xl space-y-6">

            {{-- Header --}}
            <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Gestão da biblioteca</p>
                <h1 class="mt-1 text-3xl font-semibold text-[#241b14] dark:text-white">Painel do Bibliotecário</h1>
                <p class="mt-1 text-sm text-[#66594d] dark:text-[#cfc5ba]">Confirme levantamentos, devoluções e gerencie multas.</p>
            </div>

            {{-- Mensagens de feedback --}}
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                @php
                    $statCards = [
                        ['label' => 'Pendentes', 'value' => $stats['pending'], 'color' => $stats['pending'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-[#66594d] dark:text-[#cfc5ba]'],
                        ['label' => 'A levantar', 'value' => $stats['awaiting_pickup'], 'color' => $stats['awaiting_pickup'] > 0 ? 'text-[#9b6b3f]' : 'text-[#66594d] dark:text-[#cfc5ba]'],
                        ['label' => 'Em uso', 'value' => $stats['active'], 'color' => 'text-[#9b6b3f]'],
                        ['label' => 'Em atraso', 'value' => $stats['overdue'], 'color' => $stats['overdue'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'],
                        ['label' => 'Multas (€)', 'value' => number_format($stats['unpaid_fines'], 2), 'color' => $stats['unpaid_fines'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'],
                    ];
                @endphp
                @foreach ($statCards as $card)
                    <div class="rounded-2xl border border-[#eadfce] bg-white p-4 dark:border-[#27211a] dark:bg-[#090909]">
                        <p class="text-xs font-medium text-[#66594d] dark:text-[#cfc5ba]">{{ $card['label'] }}</p>
                        <p class="mt-1 text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- 1. Pendentes de aprovação --}}
            <div class="rounded-2xl border border-amber-200 bg-white dark:border-amber-900/30 dark:bg-[#090909]">
                <div class="border-b border-amber-100 px-5 py-4 dark:border-amber-900/20">
                    <h2 class="text-base font-bold text-[#241b14] dark:text-white">
                        Pedidos pendentes
                        @if ($stats['pending'] > 0)
                            <span class="ml-2 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-black text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">{{ $stats['pending'] }}</span>
                        @endif
                    </h2>
                    <p class="mt-0.5 text-xs text-[#66594d] dark:text-[#cfc5ba]">Aprovar ou recusar pedidos de empréstimo.</p>
                </div>
                <div class="divide-y divide-[#f3e4d8] dark:divide-[#1e1510]">
                    @forelse ($pending as $r)
                        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-[#241b14] dark:text-white">{{ $r->resource?->title ?? 'Recurso removido' }}</p>
                                <p class="text-xs text-[#66594d] dark:text-[#cfc5ba]">
                                    {{ $r->user?->name ?? '—' }} · solicitado {{ $r->created_at->diffForHumans() }}
                                </p>
                                <p class="text-xs text-[#9b8b7c]">{{ $r->start_date?->format('d/m/Y') }} → {{ $r->end_date?->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex shrink-0 gap-2">
                                <form method="POST" action="{{ route('librarian.reservations.approve', $r->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex min-h-9 items-center rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700">
                                        Aprovar
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('librarian.reservations.deny', $r->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex min-h-9 items-center rounded-lg border border-red-200 bg-red-50 px-3 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-300">
                                        Recusar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-[#66594d] dark:text-[#cfc5ba]">Sem pedidos pendentes.</p>
                    @endforelse
                </div>
            </div>

            {{-- 2. A aguardar levantamento --}}
            <div class="rounded-2xl border border-[#eadfce] bg-white dark:border-[#27211a] dark:bg-[#090909]">
                <div class="border-b border-[#f3e4d8] px-5 py-4 dark:border-[#1e1510]">
                    <h2 class="text-base font-bold text-[#241b14] dark:text-white">
                        A aguardar levantamento
                        @if ($stats['awaiting_pickup'] > 0)
                            <span class="ml-2 rounded-full bg-[#fff1e6] px-2 py-0.5 text-xs font-black text-[#9b6b3f] dark:bg-[#17120f]">{{ $stats['awaiting_pickup'] }}</span>
                        @endif
                    </h2>
                    <p class="mt-0.5 text-xs text-[#66594d] dark:text-[#cfc5ba]">Confirmar quando o utilizador levanta fisicamente o recurso.</p>
                </div>
                <div class="divide-y divide-[#f3e4d8] dark:divide-[#1e1510]">
                    @forelse ($awaitingPickup as $r)
                        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-[#241b14] dark:text-white">{{ $r->resource?->title ?? 'Recurso removido' }}</p>
                                <p class="text-xs text-[#66594d] dark:text-[#cfc5ba]">
                                    {{ $r->user?->name ?? '—' }} · aprovado {{ $r->approved_at?->diffForHumans() }}
                                </p>
                                <p class="text-xs text-[#9b8b7c]">Prazo: {{ $r->end_date?->format('d/m/Y') }}</p>
                            </div>
                            <form method="POST" action="{{ route('librarian.reservations.pickup', $r->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="inline-flex min-h-9 items-center rounded-lg bg-[#FE6807] px-4 text-xs font-bold text-white hover:bg-[#e55c00]">
                                    Confirmar levantamento
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-[#66594d] dark:text-[#cfc5ba]">Nenhum recurso a aguardar levantamento.</p>
                    @endforelse
                </div>
            </div>

            {{-- 3. Em uso / Em atraso --}}
            <div class="rounded-2xl border {{ $stats['overdue'] > 0 ? 'border-red-200 dark:border-red-900/30' : 'border-[#eadfce] dark:border-[#27211a]' }} bg-white dark:bg-[#090909]">
                <div class="border-b {{ $stats['overdue'] > 0 ? 'border-red-100 dark:border-red-900/20' : 'border-[#f3e4d8] dark:border-[#1e1510]' }} px-5 py-4">
                    <h2 class="text-base font-bold text-[#241b14] dark:text-white">
                        Empréstimos ativos
                        @if ($stats['active'] > 0)
                            <span class="ml-2 rounded-full bg-[#fff1e6] px-2 py-0.5 text-xs font-black text-[#9b6b3f] dark:bg-[#17120f]">{{ $stats['active'] }}</span>
                        @endif
                        @if ($stats['overdue'] > 0)
                            <span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-black text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $stats['overdue'] }} em atraso</span>
                        @endif
                    </h2>
                    <p class="mt-0.5 text-xs text-[#66594d] dark:text-[#cfc5ba]">Confirmar quando o utilizador devolve fisicamente o recurso.</p>
                </div>
                <div class="divide-y divide-[#f3e4d8] dark:divide-[#1e1510]">
                    @forelse ($activeLoans as $r)
                        @php
                            $daysLeft = (int) now()->startOfDay()->diffInDays($r->end_date, false);
                            $isOverdue = $daysLeft < 0;
                        @endphp
                        <div class="flex flex-col gap-3 px-5 py-4 {{ $isOverdue ? 'bg-red-50/50 dark:bg-red-950/10' : '' }} sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="truncate font-semibold text-[#241b14] dark:text-white">{{ $r->resource?->title ?? 'Recurso removido' }}</p>
                                    @if ($isOverdue)
                                        <span class="shrink-0 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-black text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ abs($daysLeft) }}d atraso</span>
                                    @else
                                        <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">+{{ $daysLeft }}d</span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#66594d] dark:text-[#cfc5ba]">
                                    {{ $r->user?->name ?? '—' }} · levantado {{ $r->picked_up_at?->diffForHumans() }}
                                </p>
                                <p class="text-xs {{ $isOverdue ? 'font-semibold text-red-600 dark:text-red-400' : 'text-[#9b8b7c]' }}">
                                    Prazo: {{ $r->end_date?->format('d/m/Y') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('librarian.reservations.return', $r->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="inline-flex min-h-9 items-center rounded-lg {{ $isOverdue ? 'bg-red-600 hover:bg-red-700' : 'bg-[#241b14] hover:bg-[#3a2d22] dark:bg-[#e0cfc4] dark:text-[#241b14] dark:hover:bg-white' }} px-4 text-xs font-bold text-white">
                                    Confirmar devolução
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-[#66594d] dark:text-[#cfc5ba]">Sem empréstimos ativos.</p>
                    @endforelse
                </div>
            </div>

            {{-- 4. Multas por pagar --}}
            <div class="rounded-2xl border border-[#eadfce] bg-white dark:border-[#27211a] dark:bg-[#090909]">
                <div class="border-b border-[#f3e4d8] px-5 py-4 dark:border-[#1e1510]">
                    <h2 class="text-base font-bold text-[#241b14] dark:text-white">
                        Multas por pagar
                        @if ($stats['unpaid_fines'] > 0)
                            <span class="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-xs font-black text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ number_format($stats['unpaid_fines'], 2) }} €</span>
                        @endif
                    </h2>
                    <p class="mt-0.5 text-xs text-[#66594d] dark:text-[#cfc5ba]">Marcar como pagas após receber o pagamento.</p>
                </div>
                <div class="divide-y divide-[#f3e4d8] dark:divide-[#1e1510]">
                    @forelse ($unpaidFines as $fine)
                        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-[#241b14] dark:text-white">
                                    {{ $fine->reservation?->resource?->title ?? 'Recurso removido' }}
                                </p>
                                <p class="text-xs text-[#66594d] dark:text-[#cfc5ba]">
                                    {{ $fine->user?->name ?? '—' }} · {{ $fine->days_overdue }} {{ $fine->days_overdue === 1 ? 'dia' : 'dias' }} em atraso
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ number_format($fine->total, 2) }} €</span>
                                <form method="POST" action="{{ route('librarian.fines.pay', $fine->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex min-h-9 items-center rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700">
                                        Marcar como paga
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-emerald-600 dark:text-emerald-400">Sem multas por pagar.</p>
                    @endforelse
                </div>
            </div>

        </section>
    </main>
</body>
</html>
