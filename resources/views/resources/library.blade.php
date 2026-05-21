<!doctype html>
<html lang="pt">

<x-head title="{{ $pageTitle ?? 'Biblioteca' }}" />

@php
    $resourceItems = collect(method_exists($resources, 'items') ? $resources->items() : $resources);
    $totalResources = method_exists($resources, 'total') ? $resources->total() : $resourceItems->count();
    $pageTitle = $pageTitle ?? 'Biblioteca';
    $pageEyebrow = $pageEyebrow ?? 'Biblioteca';
    $pageDescription = $pageDescription ?? 'Recursos fisicos e digitais organizados para descoberta rapida.';
    $showOwnerActions = $showOwnerActions ?? false;
    $activeTab = $activeTab ?? null;

    $coverClasses = [
        'from-[#263238] via-[#526d5b] to-[#d9bd8b]',
        'from-[#1f2933] via-[#4b6584] to-[#b8d8d8]',
        'from-[#3f342b] via-[#7b6047] to-[#ead7b7]',
        'from-[#25324a] via-[#60708e] to-[#e1c16e]',
        'from-[#2f3a32] via-[#72836e] to-[#dfd6b8]',
        'from-[#252525] via-[#57534e] to-[#c6a664]',
    ];

    $statusLabels = [
        'available' => 'Disponível',
        'reserved' => 'Indisponível',
        'active' => 'Disponível',
    ];

    $fileTypeLabels = [
        'pdf' => 'PDF',
        'mp4' => 'Video',
        'mov' => 'Video',
        'webm' => 'Video',
        'mp3' => 'Audio',
        'ppt' => 'Slides',
        'pptx' => 'Slides',
        'doc' => 'Documento',
        'docx' => 'Documento',
        'xls' => 'Planilha',
        'xlsx' => 'Planilha',
        'zip' => 'Arquivo',
    ];

    $resourceCards = $resourceItems->values()->map(function ($resource, $index) use ($coverClasses, $statusLabels, $fileTypeLabels, $showOwnerActions, $activeTab) {
        $isDigital = $resource->type === 'digital';
        $activeReservation = $activeTab === 'borrowed' ? $resource->reservations->first() : null;
        $path = $resource->digitalResource?->file_path;
        $extension = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : null;
        $fileType = $isDigital ? ($fileTypeLabels[$extension] ?? strtoupper($extension ?: 'Digital')) : 'Livro fisico';
        $accessType = $resource->digitalResource?->access_type === 'download' ? 'Download permitido' : 'Somente visualização';
        $meta = $isDigital
            ? $accessType . ' sem prazo de devolução'
            : (($resource->physicalResource?->max_loan_days ?? 0) . ' dias de empréstimo');
        $titleWords = preg_split('/\s+/', trim($resource->title));

        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'type' => $resource->type,
            'type_label' => $isDigital ? 'Recurso digital' : 'Recurso fisico',
            'file_type_label' => $fileType,
            'category_label' => $isDigital ? $fileType : 'Biblioteca fisica',
            'resource_kind_label' => $isDigital ? 'Digital' : 'Físico',
            'meta' => $meta,
            'description' => $resource->description,
            'owner' => $resource->owner?->name ?? 'Não definido',
            'cover_class' => $coverClasses[$index % count($coverClasses)],
            'cover_image' => $resource->cover_image,
            'cover_title' => wordwrap(strtoupper(collect($titleWords)->take(3)->implode(' ')), 12, "\n", true),
            'cover_author' => strtoupper($resource->owner?->name ?? 'VUTIVI'),
            'tag' => $isDigital ? $fileType : 'Livro',
            'status' => $statusLabels[$resource->status] ?? ucfirst($resource->status),
            'available_count' => $isDigital ? null : (int) $resource->quantity_available,
            'can_manage' => auth()->check() && (int) $resource->owner_id === (int) auth()->id(),
            'is_favorited' => (bool) ($resource->is_favorited ?? false),
            'favorites_count' => (int) ($resource->favorites_count ?? 0),
            'downloads_count' => (int) ($resource->downloads_count ?? 0),
            'loans_count' => (int) ($resource->loans_count ?? 0),
            'show_owner_actions' => $showOwnerActions,
            'show_route' => $activeReservation
                ? route('reservations.show', $activeReservation->id)
                : ($resource->slug ? route('resources.public.show', $resource->slug) : route('resources.show', $resource->id)),
            'edit_route' => $isDigital ? route('digital-resources.edit', $resource->id) : route('physical-resources.edit', $resource->id),
            'delete_route' => $isDigital ? route('digital-resources.destroy', $resource->id) : route('physical-resources.destroy', $resource->id),
            'favorite_route' => route('resources.favorite', $resource->id),
            'terms_route' => !$isDigital ? route('reservations.terms.show', $resource->id) : null,
        ];
    });

@endphp

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />

    <main class="item px-3 pb-10 pt-4 sm:px-5 sm:pb-14 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => $pageTitle]]" />
        <section class="mx-auto max-w-7xl xl:max-w-[88rem]">
            <div class="surface-card p-4 dark:border-[#27211a] dark:bg-[#090909] sm:p-5 md:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-[#9b6b3f]">{{ $pageEyebrow }}
                        </p>
                        <h1 class="mt-2 text-2xl font-semibold text-[#241b14] dark:text-white sm:text-3xl md:text-4xl">
                            {{ $pageTitle }}</h1>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">
                            {{ $pageDescription }}</p>
                    </div>

                    @auth
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('resources.create') }}"
                                class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-[#FE6807] px-4 text-sm font-semibold text-white shadow-[0_12px_24px_rgba(254,104,7,0.2)] transition hover:bg-[#e15f07]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                                Adicionar recurso
                            </a>
                        </div>
                    @endauth
                </div>

                <form action="{{ url()->current() }}" method="GET"
                    class="mt-6 grid gap-3 lg:grid-cols-[minmax(260px,1.6fr)_repeat(5,minmax(130px,1fr))_auto]">
                    <label class="lg:col-span-2">
                        <span
                            class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#7f6652] dark:text-[#cfc5ba]">Pesquisar</span>
                        <div class="field-shell">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                            <input data-smart-search type="search" name="q" value="{{ request('q') }}"
                                placeholder="Titulo, autor, tag ou descricao"
                                class="premium-input text-sm placeholder:text-[#9f8c7b] dark:placeholder:text-[#80756b]">
                        </div>
                    </label>

                    <label>
                        <span
                            class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#7f6652] dark:text-[#cfc5ba]">Tipo</span>
                        <select name="type"
                            class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                            <option value="">Todos</option>
                            <option value="physical" @selected(request('type') === 'physical')>Físico</option>
                            <option value="digital" @selected(request('type') === 'digital')>Digital</option>
                        </select>
                    </label>

                    <label>
                        <span
                            class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#7f6652] dark:text-[#cfc5ba]">Formato</span>
                        <select name="format"
                            class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                            <option value="">Todos</option>
                            @foreach (['book' => 'Livro', 'pdf' => 'PDF', 'docx' => 'DOCX', 'mp4' => 'Video', 'mp3' => 'Audio', 'pptx' => 'Slides'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('format') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span
                            class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#7f6652] dark:text-[#cfc5ba]">Estado</span>
                        <select name="status"
                            class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                            <option value="">Todos</option>
                            <option value="available" @selected(request('status') === 'available')>Disponível</option>
                            <option value="reserved" @selected(request('status') === 'reserved')>Reservado</option>
                            <option value="active" @selected(request('status') === 'active')>Em uso</option>
                        </select>
                    </label>

                    <label>
                        <span
                            class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#7f6652] dark:text-[#cfc5ba]">Vista</span>
                        <select name="per_page"
                            class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                            @foreach ([10, 12, 16] as $size)
                                <option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span
                            class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#7f6652] dark:text-[#cfc5ba]">Ordenar</span>
                        <select name="sort"
                            class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                            <option value="">Recentes</option>
                            <option value="downloads" @selected(request('sort') === 'downloads')>Mais baixados</option>
                            <option value="favorites" @selected(request('sort') === 'favorites')>Mais favoritados</option>
                            <option value="popular" @selected(request('sort') === 'popular')>Populares</option>
                        </select>
                    </label>

                    <div class="flex items-end gap-2">
                        <button
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#FE6807] px-4 text-sm font-semibold text-white transition hover:bg-[#e15f07]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ url()->current() }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#decbb8] px-3 text-sm font-semibold text-[#5f4632] dark:border-[#332820] dark:text-[#d8cec3]">Limpar</a>
                    </div>
                </form>
            </div>

            @if ($resourceCards->isEmpty())
                <div
                    class="mt-5 rounded-2xl border border-[#eadfce] bg-white p-8 text-center text-sm font-semibold text-[#66594d] shadow-[0_18px_50px_rgba(54,39,25,0.06)] dark:border-[#27211a] dark:bg-[#090909] dark:text-[#cfc5ba]">
                    Nenhum recurso encontrado com estes filtros.
                </div>
            @else
                <div class="mt-5 grid items-start gap-4 lg:grid-cols-2 xl:gap-5">
                    @foreach ($resourceCards as $resource)
                        <x-resources.resource-card :resource="$resource" />
                    @endforeach
                </div>
            @endif

            @if (method_exists($resources, 'links'))
                <div
                    class="mt-6 rounded-2xl border border-[#eadfce] bg-white p-4 shadow-[0_14px_34px_rgba(54,39,25,0.06)] dark:border-[#27211a] dark:bg-[#090909]">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-semibold text-[#66594d] dark:text-[#cfc5ba]">
                            Página <span class="font-black text-[#FE6807]">{{ $resources->currentPage() }}</span> de <span
                                class="font-black text-[#241b14] dark:text-white">{{ $resources->lastPage() }}</span>
                            · {{ $resources->total() }} recursos
                        </p>
                        <div class="flex items-center gap-2">
                            @if ($resources->onFirstPage())
                                <span
                                    class="inline-flex min-h-10 items-center rounded-xl border border-[#eadfce] px-4 text-sm font-bold text-[#b7a797] opacity-60 dark:border-[#332820]">Anterior</span>
                            @else
                                <a href="{{ $resources->previousPageUrl() }}"
                                    class="inline-flex min-h-10 items-center rounded-xl border border-[#eadfce] px-4 text-sm font-bold text-[#5f4632] hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#332820] dark:text-[#d8cec3]">Anterior</a>
                            @endif
                            <div class="hidden sm:block">{{ $resources->onEachSide(1)->links() }}</div>
                            @if ($resources->hasMorePages())
                                <a href="{{ $resources->nextPageUrl() }}"
                                    class="inline-flex min-h-10 items-center rounded-xl bg-[#FE6807] px-4 text-sm font-bold text-white shadow-[0_12px_24px_rgba(254,104,7,0.18)] hover:bg-[#e15f07]">Próxima</a>
                            @else
                                <span
                                    class="inline-flex min-h-10 items-center rounded-xl bg-[#f3e4d8] px-4 text-sm font-bold text-[#b7a797] dark:bg-[#241915]">Próxima</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>

</html>