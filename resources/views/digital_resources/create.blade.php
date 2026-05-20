<!doctype html>
<html lang="pt">

<x-head title="Adicionar recurso digital" />

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />

    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Biblioteca', 'url' => route('library')], ['label' => 'Adicionar recurso digital']]" />
        <section class="surface-card mx-auto max-w-7xl p-6 dark:border-[#27211a] dark:bg-[#090909] md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#9b6b3f]">Biblioteca digital</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">Adicionar recurso digital</h1>
            <p class="mt-2 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">Ficheiros digitais não precisam de devolução, mas podem ser limitados para visualização ou download.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('digital-resources.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-6">
                @csrf
                @include('digital_resources.form', ['resource' => null, 'digital' => null, 'buttonLabel' => 'Guardar recurso digital'])
            </form>
        </section>
    </main>
</body>

</html>
