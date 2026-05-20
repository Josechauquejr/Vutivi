<!doctype html>
<html lang="pt">

<x-head title="Editar recurso fisico" />

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />

    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <section class="surface-card mx-auto max-w-7xl p-6 dark:border-[#27211a] dark:bg-[#090909] md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#9b6b3f]">Meus recursos</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">Editar recurso fisico</h1>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('physical-resources.update', $resource->id) }}" enctype="multipart/form-data" class="mt-6 grid gap-6">
                @csrf
                @method('PUT')
                @include('physical_resources.form', ['resource' => $resource, 'physical' => $resource->physicalResource, 'buttonLabel' => 'Atualizar recurso fisico'])
            </form>
        </section>
    </main>
</body>

</html>
