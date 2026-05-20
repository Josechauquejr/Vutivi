<!doctype html>
<html lang="pt">

<x-head title="Adicionar recurso" />

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />

    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Biblioteca', 'url' => route('library')], ['label' => 'Adicionar recurso']]" />
        <section class="surface-card mx-auto max-w-6xl p-6 dark:border-[#27211a] dark:bg-[#090909] md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#9b6b3f]">Adicionar recurso</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">Que tipo de recurso deseja adicionar?</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">
                Use uma entrada unica para manter a biblioteca simples. Depois escolha se o material e fisico ou digital.
            </p>

            <div class="mt-8 grid gap-4 md:grid-cols-2">
                <a href="{{ route('physical-resources.create') }}"
                    class="group rounded-xl border border-[#eadfce] bg-[#fffaf5] p-6 transition hover:-translate-y-1 hover:border-[#FE6807] hover:bg-white dark:border-[#332820] dark:bg-[#0f0d0b] dark:hover:bg-[#050505]">
                    <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-white text-[#8a6a4d] shadow-sm dark:bg-[#050505] dark:text-[#d8cec3]">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 4.5A3.5 3.5 0 0 1 5.5 3H11v18H5.5A3.5 3.5 0 0 0 2 22.5v-18Z"/><path d="M22 4.5A3.5 3.5 0 0 0 18.5 3H13v18h5.5a3.5 3.5 0 0 1 3.5 1.5v-18Z"/></svg>
                    </span>
                    <h2 class="mt-5 text-xl font-semibold text-[#241b14] dark:text-white">Recurso fisico</h2>
                    <p class="mt-2 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">Livros, equipamentos, apostilas ou materiais com localização, prazo de devolução e termos de empréstimo.</p>
                </a>

                <a href="{{ route('digital-resources.create') }}"
                    class="group rounded-xl border border-[#eadfce] bg-[#fffaf5] p-6 transition hover:-translate-y-1 hover:border-[#FE6807] hover:bg-white dark:border-[#332820] dark:bg-[#0f0d0b] dark:hover:bg-[#050505]">
                    <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-white text-[#8a6a4d] shadow-sm dark:bg-[#050505] dark:text-[#d8cec3]">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="m9 15 3 3 3-3"/></svg>
                    </span>
                    <h2 class="mt-5 text-xl font-semibold text-[#241b14] dark:text-white">Recurso digital</h2>
                    <p class="mt-2 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">PDFs, documentos, videos, slides e outros ficheiros com permissao de download ou somente visualizacao.</p>
                </a>
            </div>
        </section>
    </main>
</body>

</html>
