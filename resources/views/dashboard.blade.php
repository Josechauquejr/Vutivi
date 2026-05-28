<!doctype html>
<html lang="pt">
<x-head title="O meu painel" />

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Dashboard pessoal']]" />
        <section class="mx-auto max-w-5xl space-y-6">

            {{-- Header --}}
            <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Painel pessoal</p>
                <h1 class="mt-1 text-3xl font-semibold text-[#241b14] dark:text-white">Olá, {{ $user->name }}</h1>
                <p class="mt-1 text-sm text-[#66594d] dark:text-[#cfc5ba]">Resumo da sua actividade na biblioteca.</p>
            </div>

            {{-- Stats grid --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @php
                    $stats = [
                        ['label' => 'Total pedidos', 'value' => $totalBorrowed, 'color' => 'text-[#9b6b3f]'],
                        ['label' => 'Devolvidos', 'value' => $totalReturned, 'color' => 'text-emerald-600 dark:text-emerald-400'],
                        ['label' => 'Ativos', 'value' => $activeLoans->count(), 'color' => $activeLoans->count() > 0 ? 'text-[#9b6b3f]' : 'text-[#66594d] dark:text-[#cfc5ba]'],
                        ['label' => 'Em atraso', 'value' => $overdueLoans, 'color' => $overdueLoans > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'],
                    ];
                @endphp
                @foreach ($stats as $stat)
                    <div class="rounded-2xl border border-[#eadfce] bg-white p-4 dark:border-[#27211a] dark:bg-[#090909]">
                        <p class="text-xs font-medium text-[#66594d] dark:text-[#cfc5ba]">{{ $stat['label'] }}</p>
                        <p class="mt-1 text-3xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Multas pendentes --}}
            @if ($unpaidFines > 0)
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-950/20">
                    <p class="font-semibold text-red-800 dark:text-red-200">Multas pendentes:
                        {{ number_format($unpaidFines, 2) }} €</p>
                    <p class="mt-0.5 text-sm text-red-700 dark:text-red-300">Regularize as multas para continuar a pedir
                        empréstimos.</p>
                    <a href="{{ route('fines.index') }}"
                        class="mt-2 inline-flex min-h-9 items-center rounded-lg bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-700">Ver
                        multas</a>
                </div>
            @endif

            {{-- Empréstimos activos --}}
            @if ($activeLoans->isNotEmpty())
                <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                    <h2 class="text-lg font-semibold text-[#241b14] dark:text-white">Empréstimos activos</h2>
                    <div class="mt-3 space-y-2">
                        @foreach ($activeLoans as $loan)
                                            @php
                                                $daysLeft = (int) now()->startOfDay()->diffInDays($loan->end_date, false);
                                                $isOverdue = $daysLeft < 0;
                                            @endphp
                             <div
                                                class="flex items-center justify-between rounded-lg border {{ $isOverdue ? 'border-red-200 bg-red-50 dark:border-red-900/30 dark:bg-red-950/10' : 'border-[#eadfce] bg-[#fffaf5] dark:border-[#332820] dark:bg-[#0f0d0b]' }} p-3">
                                                <div class="min-w-0">
                                                    <p class="truncate font-medium text-[#241b14] dark:text-white">
                                                        {{ $loan->resource?->title ?? 'Recurso removido' }}</p>
                                                    <p class="text-xs text-[#66594d] dark:text-[#cfc5ba]">Devolução:
                                                        {{ $loan->end_date->format('d/m/Y') }}</p>
                                                </div>
                                                <span
                                                    class="ml-3 shrink-0 rounded-full px-2 py-0.5 text-xs font-bold {{ $isOverdue ? 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                                    {{ $isOverdue ? abs($daysLeft) . ' dias atraso' : '+' . $daysLeft . ' dias' }}
                                                </span>
                                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('borrowed') }}"
                        class="mt-3 inline-flex text-sm font-semibold text-[#9b6b3f] hover:underline">Ver todos →</a>
                </div>
            @endif

            {{-- Recursos mais emprestados --}}
            @if ($topBorrowed->isNotEmpty())
                <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                    <h2 class="text-lg font-semibold text-[#241b14] dark:text-white">Mais pedidos</h2>
                    <p class="mt-0.5 text-sm text-[#66594d] dark:text-[#cfc5ba]">Recursos que pediu com mais frequência.</p>
                    <div class="mt-3 space-y-2">
                        @foreach ($topBorrowed as $item)
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#f5ede3] text-xs font-bold text-[#9b6b3f] dark:bg-[#1f1710]">{{ $loop->iteration }}</span>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ $item->resource?->slug ? route('resources.public.show', $item->resource->slug) : '#' }}"
                                        class="truncate font-medium text-[#241b14] hover:underline dark:text-white">
                                        {{ $item->resource?->title ?? 'Recurso removido' }}
                                    </a>
                                </div>
                                <span
                                    class="shrink-0 text-sm text-[#66594d] dark:text-[#cfc5ba]">{{ $item->borrow_count }}×</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Secção do dono de recursos --}}
            @if ($ownedResources > 0)
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                        <p class="text-sm text-[#66594d] dark:text-[#cfc5ba]">Os seus recursos</p>
                        <p class="mt-1 text-3xl font-bold text-[#9b6b3f]">{{ $ownedResources }}</p>
                        <a href="{{ route('mine') }}"
                            class="mt-2 inline-flex text-sm font-semibold text-[#9b6b3f] hover:underline">Gerir →</a>
                    </div>
                    <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                        <p class="text-sm text-[#66594d] dark:text-[#cfc5ba]">Pedidos pendentes</p>
                        <p
                            class="mt-1 text-3xl font-bold {{ $pendingRequests > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-[#241b14] dark:text-white' }}">
                            {{ $pendingRequests }}</p>
                        <a href="{{ route('reservations.index') }}"
                            class="mt-2 inline-flex text-sm font-semibold text-[#9b6b3f] hover:underline">Ver pedidos →</a>
                    </div>
                </div>
            @endif

            {{-- Avaliações recentes --}}
            @if ($recentReviews->isNotEmpty())
                <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                    <h2 class="text-lg font-semibold text-[#241b14] dark:text-white">As suas avaliações</h2>
                    <div class="mt-3 space-y-3">
                        @foreach ($recentReviews as $review)
                            <div
                                class="rounded-lg border border-[#eadfce] bg-[#fffaf5] p-3 dark:border-[#332820] dark:bg-[#0f0d0b]">
                                <div class="flex items-start justify-between gap-2">
                                    <a href="{{ $review->resource?->slug ? route('resources.public.show', $review->resource->slug) : '#' }}"
                                        class="font-medium text-[#241b14] hover:underline dark:text-white">{{ $review->resource?->title }}</a>
                                    <span
                                        class="shrink-0 text-amber-500">{{ str_repeat('★', $review->stars) }}{{ str_repeat('☆', 5 - $review->stars) }}</span>
                                </div>
                                @if ($review->body)
                                    <p class="mt-1 text-sm text-[#66594d] dark:text-[#cfc5ba]">{{ $review->body }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </section>
    </main>
</body>

</html>