<!doctype html>
<html lang="pt">
<x-head title="Relatórios" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Relatórios']]" />
        <section class="mx-auto max-w-4xl space-y-8">

            {{-- Hero header --}}
            <div class="relative overflow-hidden rounded-2xl border border-[#eadfce] bg-white p-6 dark:border-[#27211a] dark:bg-[#090909]">
                <div class="pointer-events-none absolute -right-10 -top-10 h-48 w-48 rounded-full bg-[#FE6807]/5 blur-2xl dark:bg-[#FE6807]/10"></div>
                <div class="pointer-events-none absolute -bottom-6 right-24 h-32 w-32 rounded-full bg-[#FE6807]/4 blur-xl"></div>
                <div class="relative">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Centro de dados</p>
                            <h1 class="mt-1 text-3xl font-semibold text-[#241b14] dark:text-white">Relatórios</h1>
                            <p class="mt-1.5 max-w-md text-sm text-[#66594d] dark:text-[#cfc5ba]">
                                Exporte os seus dados de empréstimos e multas para análise externa.
                            </p>
                        </div>
                        <div class="hidden shrink-0 sm:flex h-14 w-14 items-center justify-center rounded-2xl bg-[#fff1e6] dark:bg-[#17120f]">
                            <svg class="h-7 w-7 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-[#eadfce] bg-[#fffaf5] px-3 py-1 text-xs font-semibold text-[#66594d] dark:border-[#332820] dark:bg-[#111] dark:text-[#cfc5ba]">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
                            Formato CSV
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-[#eadfce] bg-[#fffaf5] px-3 py-1 text-xs font-semibold text-[#66594d] dark:border-[#332820] dark:bg-[#111] dark:text-[#cfc5ba]">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                            Compatível com Excel
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-[#eadfce] bg-[#fffaf5] px-3 py-1 text-xs font-semibold text-[#66594d] dark:border-[#332820] dark:bg-[#111] dark:text-[#cfc5ba]">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            UTF-8
                        </span>
                    </div>
                </div>
            </div>

            {{-- Relatórios pessoais --}}
            @php
                $mine = [
                    [
                        'label'       => 'Empréstimos ativos',
                        'description' => 'Recursos que tem atualmente em sua posse.',
                        'count'       => $myActiveCount,
                        'route'       => route('reports.active-loans', ['mine' => 1]),
                        'accent'      => 'text-[#FE6807]',
                        'bg'          => 'bg-[#fff1e6] dark:bg-[#17120f]',
                        'border'      => 'border-[#ffd8bf] dark:border-[#4a2a1b]',
                        'svg'         => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>',
                    ],
                    [
                        'label'       => 'Em atraso',
                        'description' => 'Empréstimos cujo prazo de devolução já passou.',
                        'count'       => $myOverdueCount,
                        'route'       => route('reports.overdue-loans', ['mine' => 1]),
                        'accent'      => 'text-red-600 dark:text-red-400',
                        'bg'          => 'bg-red-50 dark:bg-red-950/30',
                        'border'      => 'border-red-200 dark:border-red-900/40',
                        'svg'         => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
                    ],
                    [
                        'label'       => 'Histórico completo',
                        'description' => 'Todos os pedidos de empréstimo que já efetuou.',
                        'count'       => $myHistoryCount,
                        'route'       => route('reports.loan-history', ['mine' => 1]),
                        'accent'      => 'text-[#9b6b3f]',
                        'bg'          => 'bg-[#fffaf5] dark:bg-[#0f0d0b]',
                        'border'      => 'border-[#eadfce] dark:border-[#332820]',
                        'svg'         => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/><path d="M12 7v5l3 2"/>',
                    ],
                    [
                        'label'       => 'Multas',
                        'description' => 'Registo completo de todas as suas multas.',
                        'count'       => $myFinesCount,
                        'route'       => route('reports.fines', ['mine' => 1]),
                        'accent'      => 'text-amber-600 dark:text-amber-400',
                        'bg'          => 'bg-amber-50 dark:bg-amber-950/20',
                        'border'      => 'border-amber-200 dark:border-amber-900/30',
                        'svg'         => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
                    ],
                ];
            @endphp

            <div>
                <div class="mb-5 flex items-center gap-3">
                    <h2 class="text-lg font-bold text-[#241b14] dark:text-white">Os meus dados</h2>
                    <span class="h-px flex-1 bg-[#eadfce] dark:bg-[#27211a]"></span>
                    <span class="text-xs text-[#9b8b7c]">apenas os seus registos</span>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($mine as $r)
                        <div class="group relative flex flex-col rounded-2xl border {{ $r['border'] }} bg-white p-5 transition duration-200 hover:shadow-[0_8px_30px_rgba(88,44,14,0.10)] dark:bg-[#090909]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $r['bg'] }}">
                                    <svg class="h-5 w-5 {{ $r['accent'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $r['svg'] !!}</svg>
                                </div>
                                <span class="mt-0.5 rounded-full {{ $r['bg'] }} px-2.5 py-0.5 text-xs font-black {{ $r['accent'] }}">
                                    {{ $r['count'] }} {{ $r['count'] === 1 ? 'registo' : 'registos' }}
                                </span>
                            </div>
                            <div class="mt-4 flex-1">
                                <p class="font-bold text-[#241b14] dark:text-white">{{ $r['label'] }}</p>
                                <p class="mt-1 text-xs leading-relaxed text-[#66594d] dark:text-[#cfc5ba]">{{ $r['description'] }}</p>
                            </div>
                            <a href="{{ $r['route'] }}"
                               class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-[#eadfce] bg-[#fbfaf7] py-2.5 text-sm font-bold text-[#4d382b] transition hover:border-[#FE6807] hover:bg-[#fff1e6] hover:text-[#FE6807] dark:border-[#27211a] dark:bg-[#111] dark:text-[#e9ddd2] dark:hover:border-[#FE6807]/40 dark:hover:text-[#FE6807]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>
                                </svg>
                                Descarregar CSV
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Relatórios globais (bibliotecário/admin) --}}
            @if ($isManager)
                @php
                    $global = [
                        [
                            'label'       => 'Todos os empréstimos ativos',
                            'description' => 'Recursos em posse de qualquer utilizador da plataforma.',
                            'count'       => $allActiveCount,
                            'route'       => route('reports.active-loans'),
                            'svg'         => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>',
                        ],
                        [
                            'label'       => 'Todos os atrasos',
                            'description' => 'Empréstimos fora do prazo em toda a plataforma.',
                            'count'       => $allOverdueCount,
                            'route'       => route('reports.overdue-loans'),
                            'svg'         => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
                        ],
                        [
                            'label'       => 'Histórico global',
                            'description' => 'Todos os pedidos de empréstimo de todos os utilizadores.',
                            'count'       => $allHistoryCount,
                            'route'       => route('reports.loan-history'),
                            'svg'         => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/><path d="M12 7v5l3 2"/>',
                        ],
                        [
                            'label'       => 'Todas as multas',
                            'description' => 'Registo completo de multas de toda a plataforma.',
                            'count'       => $allFinesCount,
                            'route'       => route('reports.fines'),
                            'svg'         => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
                        ],
                    ];
                @endphp

                <div>
                    <div class="mb-4 flex items-center gap-3">
                        <h2 class="text-lg font-bold text-[#241b14] dark:text-white">Dados globais</h2>
                        <span class="h-px flex-1 bg-[#eadfce] dark:bg-[#27211a]"></span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#fff1e6] px-2.5 py-1 text-xs font-bold text-[#9b6b3f] dark:bg-[#17120f]">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Bibliotecário
                        </span>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($global as $r)
                            <div class="group relative flex flex-col rounded-2xl border border-[#eadfce] bg-white p-5 transition duration-200 hover:shadow-[0_8px_30px_rgba(88,44,14,0.10)] dark:border-[#27211a] dark:bg-[#090909]">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#fff1e6] dark:bg-[#17120f]">
                                        <svg class="h-5 w-5 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $r['svg'] !!}</svg>
                                    </div>
                                    <span class="mt-0.5 rounded-full bg-[#fff1e6] px-2.5 py-0.5 text-xs font-black text-[#9b6b3f] dark:bg-[#17120f]">
                                        {{ $r['count'] }} {{ $r['count'] === 1 ? 'registo' : 'registos' }}
                                    </span>
                                </div>
                                <div class="mt-4 flex-1">
                                    <p class="font-bold text-[#241b14] dark:text-white">{{ $r['label'] }}</p>
                                    <p class="mt-1 text-xs leading-relaxed text-[#66594d] dark:text-[#cfc5ba]">{{ $r['description'] }}</p>
                                </div>
                                <a href="{{ $r['route'] }}"
                                   class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-[#eadfce] bg-[#fbfaf7] py-2.5 text-sm font-bold text-[#4d382b] transition hover:border-[#FE6807] hover:bg-[#fff1e6] hover:text-[#FE6807] dark:border-[#27211a] dark:bg-[#111] dark:text-[#e9ddd2] dark:hover:border-[#FE6807]/40 dark:hover:text-[#FE6807]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>
                                    </svg>
                                    Descarregar CSV
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Nota de rodapé --}}
            <p class="text-center text-xs text-[#9b8b7c]">
                Os ficheiros CSV são gerados em tempo real com os dados mais recentes.
                Separador de campo: ponto e vírgula (;) · Codificação: UTF-8 com BOM.
            </p>

        </section>
    </main>
</body>
</html>
