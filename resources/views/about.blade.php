<!doctype html>
<html lang="pt">

<x-head title="Sobre a VUTIVI" />

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />

    <main class="item px-3 pb-12 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Sobre']]" />
        <section class="surface-card mx-auto max-w-5xl overflow-hidden p-6 dark:border-[#27211a] dark:bg-[#090909] md:p-10">
            <p class="text-xs font-black uppercase tracking-[0.28em] text-[#9b6b3f]">Identidade da plataforma</p>
            <h1 class="mt-3 text-4xl font-semibold text-[#241b14] dark:text-white sm:text-5xl">VUTIVI significa conhecimento.</h1>
            <p class="mt-5 text-lg leading-8 text-[#66594d] dark:text-[#cfc5ba]">
                VUTIVI é uma plataforma moderna de biblioteca digital e física focada em gestão, descoberta e partilha de conhecimento. Inspirado na palavra xitsonga “Vutivi”, que significa conhecimento, o sistema conecta utilizadores, recursos e aprendizagem através de uma experiência moderna, organizada e inteligente.
            </p>

            <div class="mt-10 grid gap-4 md:grid-cols-4">
                @foreach ([
                    ['Partilha', 'Recursos circulam entre utilizadores com responsabilidade e rastreabilidade.'],
                    ['Aprendizagem', 'Cada recurso é apresentado com contexto, estado e caminho claro de acesso.'],
                    ['Colaboração', 'Favoritos, notificações e empréstimos aproximam pessoas e materiais.'],
                    ['Acesso moderno', 'Pesquisa, slugs amigáveis e interface responsiva tornam a informação mais acessível.'],
                ] as [$title, $copy])
                    <article class="rounded-2xl border border-[#eadfce] bg-[#fffaf5] p-5 dark:border-[#332820] dark:bg-[#0f0d0b]">
                        <h2 class="text-xl font-semibold text-[#241b14] dark:text-white">{{ $title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">{{ $copy }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</body>
</html>
