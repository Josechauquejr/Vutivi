<!doctype html>
<html lang="pt">

<x-head title="VUTIVI — Biblioteca Académica Digital e Física">
    <style>
        /* ── Scroll reveal ── */
        [data-reveal] { opacity: 0; transform: translateY(28px); transition: opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1); }
        [data-reveal].is-visible { opacity: 1; transform: none; }
        [data-reveal][data-delay="1"] { transition-delay: .08s; }
        [data-reveal][data-delay="2"] { transition-delay: .16s; }
        [data-reveal][data-delay="3"] { transition-delay: .24s; }
        [data-reveal][data-delay="4"] { transition-delay: .32s; }
        [data-reveal][data-delay="5"] { transition-delay: .40s; }

        /* ── Hero entrances ── */
        @keyframes slide-up  { from { opacity:0; transform:translateY(36px); } to { opacity:1; transform:none; } }
        @keyframes fade-in   { from { opacity:0; } to { opacity:1; } }
        @keyframes float-y   { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-14px); } }
        @keyframes scan-x    { 0% { transform:translateX(-110%); } 100% { transform:translateX(260%); } }
        @keyframes badge-in  { from { opacity:0; transform:scale(.8) translateY(6px); } to { opacity:1; transform:scale(1) translateY(0); } }
        @keyframes bounce-y  { 0%,100% { transform:translateY(0) translateX(-50%); } 50% { transform:translateY(8px) translateX(-50%); } }

        .hero-eyebrow { animation: slide-up .65s cubic-bezier(.16,1,.3,1) both; }
        .hero-h1      { animation: slide-up .65s .10s cubic-bezier(.16,1,.3,1) both; }
        .hero-sub     { animation: slide-up .65s .20s cubic-bezier(.16,1,.3,1) both; }
        .hero-form    { animation: slide-up .65s .30s cubic-bezier(.16,1,.3,1) both; }
        .hero-cta     { animation: slide-up .65s .40s cubic-bezier(.16,1,.3,1) both; }
        .hero-trust   { animation: slide-up .65s .50s cubic-bezier(.16,1,.3,1) both; }
        .hero-cards   { animation: fade-in .9s .55s both; }

        .float-a { animation: float-y 7s ease-in-out infinite; }
        .float-b { animation: float-y 9s -3.2s ease-in-out infinite; }
        .float-c { animation: float-y 11s -1.8s ease-in-out infinite; }

        .scan-beam { animation: scan-x 13s ease-in-out infinite; }
        .scroll-caret { animation: bounce-y 1.8s ease-in-out infinite; position:absolute; bottom:28px; left:50%; }

        /* ── Cards ── */
        .type-card  { transition: transform .25s ease, box-shadow .25s ease; }
        .type-card:hover  { transform: translateY(-6px); box-shadow: 0 30px 60px rgba(54,39,25,.12); }
        .feat-card  { transition: transform .22s ease, border-color .22s ease; }
        .feat-card:hover  { transform: translateY(-4px); border-color: rgba(254,104,7,.5); }
        .step-card  { transition: transform .22s ease; }
        .step-card:hover  { transform: translateY(-4px); }

        /* ── Tech badges ── */
        .tech-badge { animation: badge-in .4s cubic-bezier(.16,1,.3,1) both; }
    </style>
</x-head>

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505] overflow-x-hidden">
    <x-navbar />

    <main>

        {{-- ══════════════════════════════════════════════════════════
             § 1  HERO
        ══════════════════════════════════════════════════════════ --}}
        <section class="relative min-h-[calc(100vh-64px)] overflow-hidden bg-cover bg-center"
                 style="background-image:url('{{ asset('img/png/book_background.png') }}')">

            {{-- Layered overlays --}}
            <div class="absolute inset-0 bg-[linear-gradient(110deg,rgba(10,7,4,.94)_0%,rgba(10,7,4,.72)_46%,rgba(10,7,4,.22)_100%)]"></div>
            <div class="scan-beam pointer-events-none absolute inset-y-0 left-0 w-28 bg-gradient-to-r from-transparent via-white/6 to-transparent blur-xl"></div>

            <div class="relative mx-auto grid min-h-[calc(100vh-64px)] max-w-8xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-[1.1fr_.9fr] lg:gap-20 lg:px-10 xl:px-16">

                {{-- ── Left: copy ── --}}
                <div class="text-white">

                    <p class="hero-eyebrow inline-flex items-center gap-2 rounded-full border border-[#FE6807]/35 bg-[#FE6807]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[.3em] text-[#ffc49b]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#FE6807] shadow-[0_0_8px_#FE6807]"></span>
                        Plataforma académica · VUTIVI
                    </p>

                    <h1 class="hero-h1 mt-6 text-4xl font-bold leading-[1.1] tracking-tight sm:text-5xl lg:text-[3.75rem] xl:text-[4.5rem]">
                        O conhecimento que<br>
                        <span class="bg-gradient-to-r from-[#FE6807] via-[#ff9147] to-[#ffc49b] bg-clip-text text-transparent">procuras está aqui</span>,<br>
                        organizado para ti.
                    </h1>

                    <p class="hero-sub mt-6 max-w-xl text-base leading-8 text-[#e0d0c2] sm:text-lg">
                        VUTIVI é a plataforma de biblioteca académica que conecta estudantes a recursos digitais e físicos — com empréstimos rastreados, prazos visíveis e pesquisa inteligente.
                    </p>

                    <form action="{{ route('library') }}" method="GET" class="hero-form mt-8 flex max-w-lg flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b8b7c]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <input type="search" name="q" placeholder="Título, autor, formato…"
                                   class="h-12 w-full rounded-xl border border-white/20 bg-white pl-10 pr-4 text-sm text-[#241b14] outline-none placeholder:text-[#9b8b7c] focus:border-[#FE6807] focus:ring-2 focus:ring-[#FE6807]/20">
                        </div>
                        <button type="submit"
                                class="inline-flex h-12 flex-shrink-0 items-center justify-center gap-2 rounded-xl bg-[#FE6807] px-6 text-sm font-bold text-white shadow-[0_12px_28px_rgba(254,104,7,.38)] transition hover:-translate-y-0.5 hover:bg-[#e15f07] active:translate-y-0">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            Pesquisar
                        </button>
                    </form>

                    <div class="hero-cta mt-5 flex flex-wrap items-center gap-3">
                        <a href="{{ route('library') }}"
                           class="inline-flex h-11 items-center gap-2 rounded-xl border border-white/22 bg-white/10 px-5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/18">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                            Explorar Biblioteca
                        </a>
                        @guest
                        <a href="{{ route('users.create') }}"
                           class="inline-flex h-11 items-center gap-2 rounded-xl border border-[#FE6807]/50 bg-transparent px-5 text-sm font-semibold text-[#ffc49b] transition hover:border-[#FE6807] hover:bg-[#FE6807]/12">
                            Criar conta grátis
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        </a>
                        @endguest
                    </div>

                    {{-- Live trust bar --}}
                    <div class="hero-trust mt-8 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-white/12 pt-6 text-xs font-semibold text-[#c0b0a0]">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                            {{ $stats['total'] }} recursos disponíveis
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                            {{ $stats['digital'] }} digitais
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2Z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7Z"/></svg>
                            {{ $stats['physical'] }} físicos
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            {{ $stats['activeLoans'] }} empréstimos activos
                        </span>
                    </div>
                </div>

                {{-- ── Right: floating UI mockup ── --}}
                <div class="hero-cards relative hidden min-h-[44rem] lg:block">

                    {{-- Card A: resource preview --}}
                    <div class="float-a absolute right-2 top-10 w-[22rem] overflow-hidden rounded-2xl border border-white/18 bg-white/95 shadow-[0_40px_100px_rgba(0,0,0,.30)] backdrop-blur">
                        <div class="h-28 bg-[linear-gradient(135deg,#1d3330,#3a7a68)] p-4">
                            <span class="rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-white">Digital · PDF</span>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-[#9b8b7c]">Metodologia de Pesquisa Científica</p>
                            <h3 class="mt-1 font-bold text-[#241b14] leading-snug">Prof. A. Mucavel · 4.2 MB</h3>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Disponível</span>
                                <span class="flex items-center gap-1 text-xs text-[#9b8b7c]">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    142 downloads
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Card B: active loan --}}
                    <div class="float-b absolute bottom-20 left-0 w-72 rounded-2xl border border-white/12 bg-[#100e0c]/92 p-5 text-white shadow-[0_40px_100px_rgba(0,0,0,.38)] backdrop-blur">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-bold uppercase tracking-[.22em] text-[#ffc49b]">Empréstimo ativo</p>
                            <span class="rounded-full bg-amber-500/22 px-2.5 py-0.5 text-[11px] font-bold text-amber-400">3 dias</span>
                        </div>
                        <p class="mt-2 text-sm font-semibold leading-snug">Manual de Laboratório de Física II</p>
                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/14">
                            <span class="block h-full w-3/5 rounded-full bg-[#FE6807]"></span>
                        </div>
                        <p class="mt-1.5 text-[11px] text-[#a89888]">4 de 7 dias utilizados</p>
                    </div>

                    {{-- Card C: notification --}}
                    <div class="float-c absolute right-8 bottom-40 w-64 rounded-2xl border border-white/14 bg-white/96 p-4 shadow-[0_20px_50px_rgba(0,0,0,.14)] backdrop-blur">
                        <div class="flex items-start gap-3">
                            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-sm font-bold">✓</span>
                            <div>
                                <p class="text-xs font-bold text-[#241b14]">Empréstimo aprovado</p>
                                <p class="mt-0.5 text-[11px] text-[#7f6652]">O recurso está pronto para utilização.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="scroll-caret text-white/35">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════
             § 2  STATS STRIP (real data)
        ══════════════════════════════════════════════════════════ --}}
        <section class="border-b border-[#e8ddd0] bg-[#faf8f5] dark:border-[#1e1a16] dark:bg-[#0a0908]">
            <div class="mx-auto grid max-w-7xl grid-cols-2 divide-x divide-[#e8ddd0] dark:divide-[#1e1a16] px-4 sm:grid-cols-4 sm:px-6 lg:px-10 xl:px-16">
                @foreach ([
                    [$stats['total'],       'Recursos totais',     'M4 19.5A2.5 2.5 0 0 1 6.5 17H20 M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z'],
                    [$stats['digital'],     'Recursos digitais',   'M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z M13 2v7h7'],
                    [$stats['physical'],    'Recursos físicos',    'M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2Z M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 1 1 3-3h7Z'],
                    [$stats['activeLoans'], 'Empréstimos activos', 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z M23 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75'],
                ] as [$value, $label, $path])
                <div class="flex flex-col items-center py-7 text-center" data-reveal>
                    <svg class="mb-2 h-5 w-5 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="{{ $path }}"/></svg>
                    <p class="text-3xl font-black text-[#241b14] dark:text-white"><span data-count-to="{{ $value }}">0</span></p>
                    <p class="mt-1 text-xs font-semibold text-[#7f6652] dark:text-[#8a7868]">{{ $label }}</p>
                </div>
                @endforeach
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════
             § 3  ABOUT VUTIVI
        ══════════════════════════════════════════════════════════ --}}
        <section id="sobre" class="bg-[#fdfaf7] px-4 py-20 sm:px-6 lg:px-10 xl:px-16 dark:bg-[#060504]">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-14 lg:grid-cols-2 lg:items-center">

                    <div>
                        <p data-reveal class="text-[11px] font-black uppercase tracking-[.3em] text-[#9b6b3f]">O que é VUTIVI</p>
                        <h2 data-reveal data-delay="1" class="mt-3 text-3xl font-bold leading-tight text-[#241b14] dark:text-white sm:text-5xl">
                            <span class="text-[#FE6807]">Vutivi</span> significa<br>conhecimento em Xitsonga.
                        </h2>
                        <p data-reveal data-delay="2" class="mt-5 text-base leading-8 text-[#66594d] dark:text-[#cfc5ba]">
                            Uma plataforma criada para estudantes académicos que precisam de encontrar, gerir e partilhar recursos de forma organizada. VUTIVI conecta livros físicos, documentos digitais e o ciclo completo de empréstimos numa experiência fluída e inteligente.
                        </p>
                        <ul data-reveal data-delay="3" class="mt-6 space-y-3">
                            @foreach ([
                                'Catálogo unificado de recursos digitais e físicos',
                                'Sistema completo de empréstimos com prazos e extensões',
                                'Notificações proactivas e alertas de devolução',
                                'Moderação de conteúdo para qualidade da biblioteca',
                            ] as $item)
                            <li class="flex items-start gap-3 text-sm text-[#66594d] dark:text-[#cfc5ba]">
                                <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-[#FE6807]/14 text-[#FE6807]">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m5 12 5 5L20 7"/></svg>
                                </span>
                                {{ $item }}
                            </li>
                            @endforeach
                        </ul>
                        <div data-reveal data-delay="4" class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('library') }}"
                               class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#FE6807] px-5 text-sm font-bold text-white shadow-[0_12px_28px_rgba(254,104,7,.28)] transition hover:-translate-y-0.5 hover:bg-[#e15f07]">
                                Ir à Biblioteca
                            </a>
                            @guest
                            <a href="{{ route('users.create') }}"
                               class="inline-flex h-11 items-center gap-2 rounded-xl border border-[#e8ddd0] px-5 text-sm font-bold text-[#241b14] dark:border-[#2d2520] dark:text-white transition hover:border-[#FE6807] hover:text-[#FE6807]">
                                Criar conta
                            </a>
                            @endguest
                        </div>
                    </div>

                    {{-- Tech stack card --}}
                    <div data-reveal data-delay="2" class="rounded-3xl border border-[#e8ddd0] bg-white p-8 shadow-[0_18px_50px_rgba(54,39,25,.07)] dark:border-[#27211a] dark:bg-[#090909]">
                        <p class="text-[11px] font-black uppercase tracking-[.28em] text-[#9b6b3f]">Construído com</p>
                        <div class="mt-5 grid grid-cols-2 gap-3">
                            {{-- Laravel --}}
                            <div class="tech-badge flex items-center gap-3 rounded-2xl border border-red-500 p-4 dark:border-red-700" style="animation-delay:0s">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-[#FF2D20]">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[#FF2D20]">Laravel</p>
                                    <p class="text-[10px] font-semibold text-[#9b6b3f]">Backend · v11</p>
                                </div>
                            </div>
                            {{-- PostgreSQL --}}
                            <div class="tech-badge flex items-center gap-3 rounded-2xl border border-blue-500 p-4 dark:border-blue-700" style="animation-delay:.08s">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-[#336791]">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[#336791]">PostgreSQL</p>
                                    <p class="text-[10px] font-semibold text-[#9b6b3f]">Base de dados</p>
                                </div>
                            </div>
                            {{-- React --}}
                            <div class="tech-badge flex items-center gap-3 rounded-2xl border border-cyan-500 p-4 dark:border-cyan-700" style="animation-delay:.16s">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-[#0891b2]">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <circle cx="12" cy="12" r="2"/><ellipse cx="12" cy="12" rx="10" ry="4"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(120 12 12)"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[#0891b2]">React</p>
                                    <p class="text-[10px] font-semibold text-[#9b6b3f]">Frontend · UI</p>
                                </div>
                            </div>
                            {{-- Tailwind CSS --}}
                            <div class="tech-badge flex items-center gap-3 rounded-2xl border border-sky-500 p-4 dark:border-sky-700" style="animation-delay:.24s">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-[#0284c7]">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[#0284c7]">Tailwind CSS</p>
                                    <p class="text-[10px] font-semibold text-[#9b6b3f]">Estilização · v3</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl border border-[#e8ddd0] p-4 dark:border-[#27211a]">
                            <p class="text-xs leading-6 text-[#7f6652] dark:text-[#8a7868]">
                                <span class="font-bold text-[#241b14] dark:text-white">Stack principal:</span>
                                PHP 8.3 · Laravel 11 · PostgreSQL · React · Tailwind CSS 3
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════
             § 4  HOW IT WORKS
        ══════════════════════════════════════════════════════════ --}}
        <section id="como-funciona" class="bg-[#f5efe6] px-4 py-20 dark:bg-[#0c0b09] sm:px-6 lg:px-10 xl:px-16">
            <div class="mx-auto max-w-7xl">

                <div class="text-center">
                    <p data-reveal class="text-[11px] font-black uppercase tracking-[.3em] text-[#9b6b3f]">Processo simplificado</p>
                    <h2 data-reveal data-delay="1" class="mt-3 text-3xl font-bold text-[#241b14] dark:text-white sm:text-5xl">Como funciona?</h2>
                    <p data-reveal data-delay="2" class="mx-auto mt-4 max-w-2xl text-base text-[#66594d] dark:text-[#cfc5ba]">Em quatro passos tens acesso completo ao sistema de recursos académicos.</p>
                </div>

                <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['01', 'Registo',       'Cria a tua conta de estudante e acede ao catálogo completo da biblioteca.',             'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z M19 8v6 M22 11h-6'],
                        ['02', 'Explorar',      'Pesquisa por título, autor ou formato. Filtra por tipo, estado e categoria.',          'M21 21l-4.3-4.3 M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z'],
                        ['03', 'Solicitar',     'Faz um pedido de empréstimo. O dono recebe e aprova com uma notificação em tempo real.','M9 12h6 M9 16h3 M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z M13 2v7h7'],
                        ['04', 'Devolver',      'Acompanha o prazo com alertas. Solicita extensão se necessário. Devolve a tempo.',     'M22 11.08V12a10 10 0 1 1-5.93-9.14 M22 4 12 14.01l-3-3'],
                    ] as [$n, $title, $copy, $path])
                    <div data-reveal data-delay="{{ $loop->index + 1 }}" class="step-card rounded-2xl border border-[#e8ddd0] bg-white p-6 dark:border-[#27211a] dark:bg-[#090909]">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff1e6] dark:bg-[#17120f]">
                            <svg class="h-5 w-5 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="{{ $path }}"/></svg>
                        </div>
                        <span class="mt-4 block text-4xl font-black text-[#FE6807]/40">{{ $n }}</span>
                        <h3 class="mt-1 text-lg font-bold text-[#241b14] dark:text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">{{ $copy }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════
             § 5  TYPES OF RESOURCES
        ══════════════════════════════════════════════════════════ --}}
        <section id="tipos-de-recursos" class="bg-[#fbfaf7] px-4 py-20 sm:px-6 lg:px-10 xl:px-16 dark:bg-[#050504]">
            <div class="mx-auto max-w-7xl">

                <div class="text-center">
                    <p data-reveal class="text-[11px] font-black uppercase tracking-[.3em] text-[#9b6b3f]">Catálogo da biblioteca</p>
                    <h2 data-reveal data-delay="1" class="mt-3 text-3xl font-bold text-[#241b14] dark:text-white sm:text-5xl">Dois tipos de recursos, uma experiência.</h2>
                </div>

                <div class="mt-12 grid gap-6 lg:grid-cols-2">

                    {{-- Digital --}}
                    <div data-reveal data-delay="1" class="type-card overflow-hidden rounded-3xl border border-[#e8ddd0] bg-white dark:border-[#27211a] dark:bg-[#090909]">
                        <div class="bg-[linear-gradient(135deg,#1a2d26,#2d4a3e,#0f1c18)] p-8 text-white">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/16 px-3 py-1 text-[11px] font-bold uppercase tracking-wider">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                                Digital
                            </span>
                            <h3 class="mt-4 text-2xl font-bold leading-snug">Documentos digitais, PDFs e ficheiros académicos.</h3>
                            <p class="mt-2 text-sm text-white/70">Acesso imediato sem filas de espera físicas.</p>
                        </div>
                        <div class="p-6">
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ([
                                    ['Download',       'Transfere para leitura offline em qualquer dispositivo.'],
                                    ['Visualização',   'Abre no browser sem necessidade de download.'],
                                    ['PDFs & DOCX',    'Suporte para os principais formatos académicos.'],
                                    ['Acesso imediato','Sem filas — disponível quando o recurso está livre.'],
                                ] as [$t, $c])
                                <div class="rounded-xl bg-[#f5f1eb] p-6 dark:bg-[#12100e]">
                                    <p class="text-xs font-bold text-[#241b14] dark:text-white">{{ $t }}</p>
                                    <p class="mt-1 text-xs text-[#66594d] dark:text-[#8a7868]">{{ $c }}</p>
                                </div>
                                @endforeach
                            </div>
                            <a href="{{ route('library', ['type' => 'digital']) }}"
                               class="mt-5 inline-flex h-10 items-center gap-2 rounded-xl bg-[#1a2d26] px-5 text-sm font-bold text-white transition hover:bg-[#FE6807]">
                                Ver recursos digitais
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Physical --}}
                    <div data-reveal data-delay="2" class="type-card overflow-hidden rounded-3xl border border-[#e8ddd0] bg-white dark:border-[#27211a] dark:bg-[#090909]">
                        <div class="bg-[linear-gradient(135deg,#3b2d24,#6b4c38,#241b14)] p-8 text-white">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/16 px-3 py-1 text-[11px] font-bold uppercase tracking-wider">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                                Físico
                            </span>
                            <h3 class="mt-4 text-2xl font-bold leading-snug">Livros, manuais e materiais de laboratório.</h3>
                            <p class="mt-2 text-sm text-white/70">Gestão completa de empréstimo, prazo e devolução.</p>
                        </div>
                        <div class="p-6">
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ([
                                    ['Localização',  'Sabe exactamente onde o recurso está fisicamente.'],
                                    ['Prazo claro',  'Data de devolução visível em todo o fluxo.'],
                                    ['Extensão',     'Solicita mais tempo com aprovação do dono.'],
                                    ['Alertas',      'Notificação automática antes do fim do prazo.'],
                                ] as [$t, $c])
                                <div class="rounded-xl bg-[#f5f1eb] p-6 dark:bg-[#12100e]">
                                    <p class="text-xs font-bold text-[#241b14] dark:text-white">{{ $t }}</p>
                                    <p class="mt-1 text-xs text-[#66594d] dark:text-[#8a7868]">{{ $c }}</p>
                                </div>
                                @endforeach
                            </div>
                            <a href="{{ route('library', ['type' => 'physical']) }}"
                               class="mt-5 inline-flex h-10 items-center gap-2 rounded-xl bg-[#3b2d24] px-5 text-sm font-bold text-white transition hover:bg-[#FE6807]">
                                Ver recursos físicos
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════
             § 6  FEATURES
        ══════════════════════════════════════════════════════════ --}}
        <section id="funcionalidades" class="bg-[#eee8df] px-4 py-20 dark:bg-[#0c0b09] sm:px-6 lg:px-10 xl:px-16">
            <div class="mx-auto max-w-7xl">

                <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
                    <div class="lg:sticky lg:top-8">
                        <p data-reveal class="text-[11px] font-black uppercase tracking-[.3em] text-[#9b6b3f]">Funcionalidades</p>
                        <h2 data-reveal data-delay="1" class="mt-3 text-3xl font-bold leading-tight text-[#241b14] dark:text-white sm:text-5xl">
                            Tudo o que um estudante precisa, num só lugar.
                        </h2>
                        <p data-reveal data-delay="2" class="mt-5 text-base leading-8 text-[#66594d] dark:text-[#cfc5ba]">
                            De pesquisa rápida a gestão de empréstimos, VUTIVI foi desenhado para reduzir o atrito e ampliar o acesso ao conhecimento académico.
                        </p>
                        <a data-reveal data-delay="3" href="{{ route('library') }}"
                           class="mt-8 inline-flex h-11 items-center gap-2 rounded-xl bg-[#FE6807] px-5 text-sm font-bold text-white shadow-[0_12px_28px_rgba(254,104,7,.28)] transition hover:-translate-y-0.5 hover:bg-[#e15f07]">
                            Explorar biblioteca
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ([
                            ['Pesquisa inteligente',     'Sugestões em tempo real enquanto escreves — por título, autor ou formato.',                         'M21 21l-4.3-4.3 M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z'],
                            ['Favoritos',                'Guarda recursos preferidos e recebe alerta quando um favorito fica disponível.',                   'm12 21-1.45-1.32C5.4 15.02 2 11.93 2 8.15 2 5.06 4.42 3 7.5 3c1.74 0 3.41.8 4.5 2.05A6.02 6.02 0 0 1 16.5 3C19.58 3 22 5.06 22 8.15c0 3.78-3.4 6.87-8.55 11.53L12 21Z'],
                            ['Gestão de empréstimos',    'Pedido → aprovação → acompanhamento → devolução, rastreado em cada passo.',                        'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z'],
                            ['Notificações',             'Alertas de prazo, aprovações e pedidos de extensão entregues em tempo real.',                      'M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9 M13.73 21a2 2 0 0 1-3.46 0'],
                            ['Área pessoal',             'Página de conta com estatísticas, recursos adicionados e histórico de empréstimos.',               'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                            ['Moderação de conteúdo',   'Sistema de aprovação para manter a qualidade e confiança do catálogo partilhado.',                 'M9 12h6 M9 16h3 M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z M13 2v7h7'],
                        ] as [$title, $copy, $path])
                        <div data-reveal data-delay="{{ $loop->index % 2 + 1 }}"
                             class="feat-card rounded-2xl border border-[#e8ddd0] bg-white p-7 dark:border-[#27211a] dark:bg-[#090909]">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#fff1e6] text-[#FE6807] dark:bg-[#17120f]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="{{ $path }}"/></svg>
                            </span>
                            <h3 class="mt-4 text-base font-bold text-[#241b14] dark:text-white">{{ $title }}</h3>
                            <p class="mt-2 text-xs leading-5 text-[#66594d] dark:text-[#8a7868]">{{ $copy }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════
             § 7  POPULAR RESOURCES (real data)
        ══════════════════════════════════════════════════════════ --}}
        @if($popularResources->isNotEmpty())
        <section class="bg-[#fdfaf7] px-4 py-20 sm:px-6 lg:px-10 xl:px-16 dark:bg-[#060504]">
            <div class="mx-auto max-w-7xl">

                <div data-reveal class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[.3em] text-[#9b6b3f]">Mais procurados</p>
                        <h2 class="mt-3 text-3xl font-bold text-[#241b14] dark:text-white sm:text-5xl">Populares na biblioteca.</h2>
                    </div>
                    <a href="{{ route('library', ['sort' => 'popular']) }}"
                       class="inline-flex h-10 items-center gap-2 rounded-xl border border-[#e8ddd0] px-5 text-sm font-bold text-[#241b14] dark:border-[#27211a] dark:text-white transition hover:border-[#FE6807] hover:text-[#FE6807]">
                        Ver todos
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach($popularResources as $resource)
                    <a data-reveal data-delay="{{ $loop->index + 1 }}"
                       href="{{ $resource->slug ? route('resources.public.show', $resource->slug) : route('resources.show', $resource->id) }}"
                       class="type-card group block overflow-hidden rounded-2xl border border-[#e8ddd0] bg-white dark:border-[#27211a] dark:bg-[#090909]">
                        <div class="relative h-44 {{ $resource->type === 'digital' ? 'bg-[linear-gradient(135deg,#1a2d26,#3a7a68)]' : 'bg-[linear-gradient(135deg,#3b2d24,#7a5840)]' }} overflow-hidden p-5 text-white">
                            <span class="rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider">
                                {{ $resource->type === 'digital' ? 'Digital' : 'Físico' }}
                            </span>
                            @if(($resource->favorites_count ?? 0) > 0)
                            <span class="absolute right-4 top-4 flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-bold">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><path d="m12 21-1.45-1.32C5.4 15.02 2 11.93 2 8.15 2 5.06 4.42 3 7.5 3c1.74 0 3.41.8 4.5 2.05A6.02 6.02 0 0 1 16.5 3C19.58 3 22 5.06 22 8.15c0 3.78-3.4 6.87-8.55 11.53L12 21Z"/></svg>
                                {{ $resource->favorites_count }}
                            </span>
                            @endif
                            <h3 class="absolute bottom-5 left-5 right-5 text-lg font-bold leading-snug transition group-hover:-translate-y-1">
                                {{ $resource->title }}
                            </h3>
                        </div>
                        <div class="flex items-center justify-between p-6">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-semibold text-[#241b14] dark:text-white">
                                    {{ $resource->authors ?? ($resource->owner?->name ?? '—') }}
                                </p>
                                @if(($resource->downloads_count ?? 0) > 0)
                                <p class="mt-0.5 text-[11px] text-[#7f6652]">↓ {{ $resource->downloads_count }} downloads</p>
                                @endif
                            </div>
                            <span class="ml-3 flex-shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold
                                {{ $resource->status === 'available' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $resource->status === 'available' ? 'Disponível' : 'Ocupado' }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>

            </div>
        </section>
        @endif

        {{-- ══════════════════════════════════════════════════════════
             § 8  CTA → LIBRARY
        ══════════════════════════════════════════════════════════ --}}
        <section class="bg-[radial-gradient(ellipse_at_30%_50%,rgba(254,104,7,.18),transparent_55%),linear-gradient(135deg,#12100e,#1e2a24_55%,#12100e)] px-4 py-24 sm:px-6 lg:px-10 xl:px-16">
            <div class="mx-auto max-w-4xl text-center text-white">
                <p data-reveal class="text-[11px] font-black uppercase tracking-[.3em] text-[#ffc49b]">Pronto para começar?</p>
                <h2 data-reveal data-delay="1" class="mt-4 text-4xl font-bold leading-tight sm:text-6xl">
                    O teu próximo recurso<br>está a um clique de distância.
                </h2>
                <p data-reveal data-delay="2" class="mx-auto mt-6 max-w-xl text-base leading-8 text-[#cfc5ba]">
                    Acede à biblioteca completa, filtra por tipo, pesquisa por autor e solicita empréstimos em segundos.
                </p>
                <div data-reveal data-delay="3" class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('library') }}"
                       class="inline-flex h-14 items-center gap-3 rounded-2xl bg-[#FE6807] px-8 text-base font-bold text-white shadow-[0_20px_50px_rgba(254,104,7,.42)] transition hover:-translate-y-1 hover:bg-[#e15f07]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                        Ir à Biblioteca
                    </a>
                    @guest
                    <a href="{{ route('users.create') }}"
                       class="inline-flex h-14 items-center gap-3 rounded-2xl border border-white/22 bg-white/10 px-8 text-base font-bold text-white backdrop-blur transition hover:bg-white/18">
                        Criar conta grátis
                    </a>
                    @endguest
                </div>
            </div>
        </section>

    </main>

    <x-footer />

    <script>
        /* Reveal on scroll */
        const revealObs = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); revealObs.unobserve(e.target); } });
        }, { threshold: 0.12 });
        document.querySelectorAll('[data-reveal]').forEach(el => revealObs.observe(el));

        /* Animated counters */
        function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }
        document.querySelectorAll('[data-count-to]').forEach(el => {
            const target = parseInt(el.dataset.countTo, 10);
            const duration = 1400;
            let start = null;
            const obs = new IntersectionObserver(([entry]) => {
                if (!entry.isIntersecting) return;
                obs.disconnect();
                function step(ts) {
                    if (!start) start = ts;
                    const p = Math.min((ts - start) / duration, 1);
                    el.textContent = Math.floor(easeOutCubic(p) * target);
                    if (p < 1) requestAnimationFrame(step);
                    else el.textContent = target;
                }
                requestAnimationFrame(step);
            }, { threshold: 0.5 });
            obs.observe(el);
        });
    </script>

</body>

</html>
