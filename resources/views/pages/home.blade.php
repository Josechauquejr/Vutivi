<!doctype html>
<html lang="pt">

<x-head title="Vutivi Library">
    <style>
        @keyframes float-up {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }

        @keyframes soft-scan {
            0% { transform: translateX(-12%); opacity: .25; }
            50% { opacity: .55; }
            100% { transform: translateX(112%); opacity: .25; }
        }

        .index-float { animation: float-up 7s ease-in-out infinite; }
        .index-scan { animation: soft-scan 9s ease-in-out infinite; }
    </style>
</x-head>

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />

    <main class="item">
        <section class="relative min-h-[calc(100vh-92px)] overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('img/png/book_background.png') }}')">
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(18,12,7,0.88)_0%,rgba(18,12,7,0.68)_42%,rgba(18,12,7,0.20)_100%)]"></div>
            <div class="index-scan pointer-events-none absolute inset-y-0 left-0 w-1/3 bg-white/10 blur-2xl"></div>

            <div class="relative grid min-h-[calc(100vh-92px)] items-center gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-10 xl:px-16">
                <div class="max-w-4xl text-white">
                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-[#ffc49b]">Biblioteca digital e fisica</p>
                    <h1 class="mt-5 text-4xl font-semibold leading-tight sm:text-6xl lg:text-7xl">
                        Conhecimento organizado para ser encontrado, partilhado e devolvido a tempo.
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-8 text-[#f2dfd0] sm:text-lg">
                        A VUTIVI transforma recursos, livros, documentos e empréstimos num ecossistema de descoberta simples, humano e confiável.
                    </p>

                    <form action="{{ route('library') }}" method="GET" class="mt-8 grid max-w-3xl gap-3 rounded-xl border border-white/20 bg-white/12 p-3 backdrop-blur md:grid-cols-[1fr_auto]">
                        <label>
                            <span class="sr-only">Pesquisar recursos</span>
                            <input type="search" name="q" placeholder="Pesquisar por titulo, autor, categoria ou formato" class="min-h-12 w-full rounded-lg border border-white/20 bg-white px-4 text-sm text-[#241b14] outline-none placeholder:text-[#8a7868] focus:border-[#FE6807]">
                        </label>
                        <button class="inline-flex min-h-12 items-center justify-center gap-2 rounded-lg bg-[#FE6807] px-5 text-sm font-semibold text-white shadow-[0_18px_36px_rgba(254,104,7,0.26)] transition hover:-translate-y-0.5 hover:bg-[#e15f07]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            Explorar Biblioteca
                        </button>
                    </form>
                </div>

                <div class="relative hidden min-h-[34rem] lg:block">
                    <div class="index-float absolute right-4 top-4 w-80 rounded-xl border border-white/20 bg-white/88 p-5 shadow-[0_30px_80px_rgba(0,0,0,0.24)] backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#9b6b3f]">Recursos em destaque</p>
                        <div class="mt-4 space-y-3">
                            @foreach (['Metodologia de Pesquisa', 'Arquivo Academico', 'Manual de Laboratorio'] as $item)
                                <div class="flex items-center gap-3 rounded-lg bg-[#fff7ef] p-3">
                                    <span class="h-11 w-8 rounded bg-[linear-gradient(135deg,#3b2d24,#b88b5d)] shadow-sm"></span>
                                    <div>
                                        <p class="text-sm font-semibold text-[#241b14]">{{ $item }}</p>
                                        <p class="text-xs text-[#7f6652]">Disponível para consulta</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="index-float absolute bottom-8 left-4 w-72 rounded-xl border border-white/20 bg-[#12100e]/82 p-5 text-white shadow-[0_30px_80px_rgba(0,0,0,0.32)] backdrop-blur" style="animation-delay: -2.2s">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffc49b]">Prazos</p>
                        <h2 class="mt-3 text-2xl font-semibold text-white">3 empréstimos terminam esta semana</h2>
                        <div class="mt-5 h-2 overflow-hidden rounded-full bg-white/20">
                            <span class="block h-full w-2/3 rounded-full bg-[#FE6807]"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-4 py-16 sm:px-6 lg:px-10 xl:px-16">
            <div data-reveal class="mx-auto grid max-w-7xl gap-8 rounded-3xl border border-[#eadfce] bg-white p-6 shadow-[0_18px_50px_rgba(54,39,25,0.08)] dark:border-[#27211a] dark:bg-[#090909] md:p-10 lg:grid-cols-[0.8fr_1.2fr]">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-[#9b6b3f]">O significado de VUTIVI</p>
                    <h2 class="mt-3 text-3xl font-semibold text-[#241b14] dark:text-white sm:text-5xl">Conhecimento que circula, ensina e aproxima pessoas.</h2>
                </div>
                <p class="text-base leading-8 text-[#66594d] dark:text-[#cfc5ba]">
                    VUTIVI é uma plataforma moderna de biblioteca digital e física focada em gestão, descoberta e partilha de conhecimento. Inspirado na palavra xitsonga “Vutivi”, que significa conhecimento, o sistema conecta utilizadores, recursos e aprendizagem através de uma experiência organizada, colaborativa e inteligente.
                </p>
            </div>
        </section>

        <section class="px-4 py-20 sm:px-6 lg:px-10 xl:px-16">
            <div class="mx-auto max-w-7xl">
                <div data-reveal class="max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-[#9b6b3f]">Descoberta de conhecimento</p>
                    <h2 class="mt-3 text-3xl font-semibold text-[#241b14] dark:text-white sm:text-5xl">Da pergunta ao recurso certo, sem perder o fio da leitura.</h2>
                </div>
                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    @foreach ([
                        ['Pesquisa inteligente', 'Sugestões, categorias e tags aproximam o estudante do material mais útil.', 'M21 21l-4.3-4.3 M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z'],
                        ['Colecoes guiadas', 'Recursos digitais e fisicos aparecem como trilhas de estudo, nao como listas soltas.', 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20 M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z'],
                        ['Favoritos vivos', 'Materiais importantes ficam guardados e voltam a avisar quando estiverem disponiveis.', 'm12 21-1.45-1.32C5.4 15.02 2 11.93 2 8.15 2 5.06 4.42 3 7.5 3c1.74 0 3.41.8 4.5 2.05A6.02 6.02 0 0 1 16.5 3C19.58 3 22 5.06 22 8.15c0 3.78-3.4 6.87-8.55 11.53L12 21Z'],
                    ] as [$title, $copy, $path])
                        <article data-reveal class="rounded-2xl border border-[#eadfce] bg-white p-6 shadow-[0_14px_34px_rgba(54,39,25,0.06)] transition hover:-translate-y-1 dark:border-[#27211a] dark:bg-[#090909]">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff1e6] text-[#FE6807] dark:bg-[#17120f]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="{{ $path }}"/></svg>
                            </span>
                            <h3 class="mt-5 text-xl font-semibold text-[#241b14] dark:text-white">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[linear-gradient(135deg,#f4ece3_0%,#eef4f1_52%,#fff7ef_100%)] px-4 py-20 dark:bg-[linear-gradient(135deg,#0c0a08_0%,#0e1512_52%,#120d0a_100%)] sm:px-6 lg:px-10 xl:px-16">
            <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                <div data-reveal>
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-[#9b6b3f]">Recursos digitais e fisicos</p>
                    <h2 class="mt-3 text-3xl font-semibold text-[#241b14] dark:text-white sm:text-5xl">Uma biblioteca hibrida, com sinais claros para cada formato.</h2>
                    <p class="mt-5 text-base leading-8 text-[#66594d] dark:text-[#cfc5ba]">PDFs, slides, videos, livros fisicos e materiais de apoio ganham identidade visual propria: disponibilidade, tipo, permissao e prazo aparecem onde a decisao acontece.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ([['Digital', 'Download, visualização e acesso sem devolução física'], ['Físico', 'Localização, prazo máximo e condições de empréstimo'], ['Disponível', 'Estados visuais para saber o que pode ser usado agora'], ['Emprestado', 'Prazos e alertas para manter a circulação organizada']] as [$title, $copy])
                        <article data-reveal class="rounded-2xl border border-white/70 bg-white/78 p-5 shadow-[0_18px_46px_rgba(54,39,25,0.08)] backdrop-blur dark:border-[#2c211a] dark:bg-[#11100e]">
                            <h3 class="text-2xl font-semibold text-[#241b14] dark:text-white">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="px-4 py-20 sm:px-6 lg:px-10 xl:px-16">
            <div class="mx-auto max-w-7xl">
                <div data-reveal class="grid gap-8 lg:grid-cols-[1fr_0.9fr] lg:items-end">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.28em] text-[#9b6b3f]">Sistema inteligente de empréstimos</p>
                        <h2 class="mt-3 text-3xl font-semibold text-[#241b14] dark:text-white sm:text-5xl">Pedidos, aprovacoes e devolucoes com menos friccao.</h2>
                    </div>
                    <p class="text-base leading-8 text-[#66594d] dark:text-[#cfc5ba]">A experiência combina alertas silenciosos, estados legíveis e microinterações para que o utilizador saiba sempre o próximo passo.</p>
                </div>
                <div class="mt-10 grid gap-4 lg:grid-cols-4">
                    @foreach ([['01', 'Solicitar'], ['02', 'Aprovar'], ['03', 'Acompanhar prazo'], ['04', 'Devolver']] as [$number, $label])
                        <div data-reveal class="rounded-2xl border border-[#eadfce] bg-white p-6 dark:border-[#27211a] dark:bg-[#090909]">
                            <span class="text-sm font-black text-[#FE6807]">{{ $number }}</span>
                            <h3 class="mt-5 text-xl font-semibold text-[#241b14] dark:text-white">{{ $label }}</h3>
                            <div class="mt-5 h-2 rounded-full bg-[#f1dfcd] dark:bg-[#241915]"><span class="block h-full rounded-full bg-[#FE6807]" style="width: {{ 25 * (int) $number }}%"></span></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[radial-gradient(circle_at_20%_10%,rgba(254,104,7,0.22),transparent_30%),linear-gradient(135deg,#11100e,#17211d_55%,#25170f)] px-4 py-20 text-white sm:px-6 lg:px-10 xl:px-16">
            <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[0.8fr_1.2fr]">
                <div data-reveal>
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-[#ffc49b]">Estatisticas da biblioteca</p>
                    <h2 class="mt-3 text-3xl font-semibold text-white sm:text-5xl">Movimento visivel para uma comunidade que aprende junta.</h2>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ([[1200, 'consultas organizadas'], [320, 'recursos digitais'], [94, 'pedidos acompanhados']] as [$value, $label])
                        <div data-reveal class="rounded-2xl border border-white/10 bg-white/8 p-6 backdrop-blur">
                            <p class="text-4xl font-black text-[#ffc49b]"><span data-count-to="{{ $value }}">0</span>{{ $value === 94 ? '%' : '+' }}</p>
                            <p class="mt-3 text-sm font-semibold text-[#f2dfd0]">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="px-4 py-20 sm:px-6 lg:px-10 xl:px-16">
            <div class="mx-auto max-w-7xl">
                <div data-reveal class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.28em] text-[#9b6b3f]">Recursos populares</p>
                        <h2 class="mt-3 text-3xl font-semibold text-[#241b14] dark:text-white sm:text-5xl">Leituras que puxam novas leituras.</h2>
                    </div>
                    <a href="{{ route('library', ['sort' => 'popular']) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#FE6807] px-5 text-sm font-bold text-white hover:bg-[#e15f07]">Ver populares</a>
                </div>
                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    @foreach (['Manual de Investigacao', 'Laboratorio Digital', 'Arquivo Academico'] as $title)
                        <article data-reveal class="group overflow-hidden rounded-2xl border border-[#eadfce] bg-white dark:border-[#27211a] dark:bg-[#090909]">
                            <div class="h-44 bg-[linear-gradient(135deg,#2d3a34,#d7b37f)] p-5 text-white">
                                <span class="rounded-full bg-white/16 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em]">Popular</span>
                                <h3 class="mt-16 text-2xl font-semibold transition group-hover:translate-y-[-4px]">{{ $title }}</h3>
                            </div>
                            <div class="p-5">
                                <p class="text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">Favoritos, acessos e empréstimos ajudam a revelar o que a comunidade está a estudar.</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <x-footer />

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>

</html>
