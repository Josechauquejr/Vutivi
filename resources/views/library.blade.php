<!doctype html>
<html lang="pt">

<x-head title="Meus Livros" />

@php
    $resourceItems = collect(method_exists($resources, 'items') ? $resources->items() : $resources);
    $totalResources = method_exists($resources, 'total') ? $resources->total() : $resourceItems->count();

    $coverClasses = [
        'from-[#2a1813] via-[#5d3528] to-[#ba9872]',
        'from-[#5b2b75] via-[#8c5ac9] to-[#f1cf61]',
        'from-[#8eb7d8] via-[#cfe3ef] to-[#f6edd7]',
        'from-[#205b8c] via-[#4f99db] to-[#f3b84b]',
        'from-[#3b6f8b] via-[#8ab6c8] to-[#f0db92]',
        'from-[#1c1c1c] via-[#434343] to-[#c8a76a]',
    ];

    $statusLabels = [
        'available' => 'Disponivel',
        'reserved' => 'Reservado',
        'active' => 'Em uso',
    ];

    $fileTypeLabels = [
        'pdf' => 'PDF',
        'mp4' => 'Video',
        'mov' => 'Video',
        'mp3' => 'MP3',
        'ppt' => 'Slides',
        'pptx' => 'Slides',
        'doc' => 'Documento',
        'docx' => 'Documento',
        'xls' => 'Planilha',
        'xlsx' => 'Planilha',
        'zip' => 'Arquivo',
    ];

    $resourceCards = $resourceItems->values()->map(function ($resource, $index) use ($coverClasses, $statusLabels, $fileTypeLabels) {
        $isDigital = $resource->type === 'digital';
        $path = $resource->digitalResource?->file_path;
        $extension = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : null;
        $fileType = $isDigital ? ($fileTypeLabels[$extension] ?? strtoupper($extension ?: 'Digital')) : 'Livro';
        $meta = $isDigital
            ? ($resource->digitalResource?->access_days . ' dias de acesso')
            : ($resource->quantity_available . ' disponiveis');
        $category = $isDigital ? $fileType : 'Livros';
        $titleWords = preg_split('/\s+/', trim($resource->title));
        $coverTitle = collect($titleWords)->take(3)->implode(' ');

        return [
            'title' => $resource->title,
            'type_label' => $isDigital ? 'Recurso digital' : 'Recurso fisico',
            'file_type_label' => $fileType,
            'category_label' => $category,
            'resource_kind_label' => $isDigital ? 'Digital' : 'Fisico',
            'meta' => $meta,
            'description' => $resource->description,
            'cover_class' => $coverClasses[$index % count($coverClasses)],
            'cover_title' => wordwrap(strtoupper($coverTitle), 12, "\n", true),
            'cover_author' => strtoupper($resource->owner?->name ?? 'VUTIVI'),
            'tag' => $isDigital ? $fileType : 'Livro',
            'status' => $statusLabels[$resource->status] ?? ucfirst($resource->status),
        ];
    });
@endphp

<body class="home-layout bg-white dark:bg-black">
    <x-navbar />

    <main class="item bg-white px-3 pb-10 pt-4 dark:bg-black sm:px-5 sm:pb-14 md:px-6 lg:px-8">
        <section
            class="relative mx-auto max-w-7xl overflow-hidden rounded-2xl border border-white/70 bg-white/90 p-4 shadow-[0_28px_70px_rgba(254,104,7,0.12)] backdrop-blur dark:border-[#241915] dark:bg-[#050505]/95 dark:shadow-[0_28px_70px_rgba(0,0,0,0.42)] sm:rounded-[28px] sm:p-5 md:p-8 xl:max-w-[88rem]">
            <div class="pointer-events-none absolute -right-12 top-0 h-40 w-40 rounded-full bg-[#FE6807]/10 blur-3xl">
            </div>
            <div class="pointer-events-none absolute -left-10 bottom-0 h-32 w-32 rounded-full bg-[#fe6807]/8 blur-3xl">
            </div>

            <div
                class="relative flex flex-col gap-5 border-b border-[#f3e4d8] pb-6 dark:border-[#241915] sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#c97d46]">Biblioteca</p>
                    <h1 class="mt-3 text-2xl font-semibold text-[#2c1c13] dark:text-white sm:text-3xl md:text-4xl">Recursos</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#7a5c4a] dark:text-[#d5c7be]">
                        Um espaco com recursos de leitura, estudo e apoio visual em vez de somente livros, com cards
                        mais uniformes.
                    </p>
                </div>

                <div class="grid w-full gap-3 sm:w-auto sm:grid-cols-2 xl:flex xl:flex-wrap">
                    <div
                        class="inline-flex min-h-11 w-full items-center gap-3 rounded-full border border-[#ffd8bf] bg-[#fff4ec] px-4 text-sm font-medium text-[#9f5627] shadow-[0_12px_24px_rgba(254,104,7,0.10)] dark:border-[#4a2a1b] dark:bg-[#120d0a] dark:text-[#ffb07a] sm:col-span-2 xl:col-span-1 xl:w-auto xl:px-5">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-[#FE6807]"></span>
                        <span>{{ $totalResources }} recursos disponiveis</span>
                    </div>

                    <div class="relative w-full">
                        <button id="resourcesFilterButton" data-dropdown-toggle="resourcesFilterDropdown" type="button"
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-full border border-[#ffd8bf] bg-white px-4 text-sm font-semibold text-[#7d4a2b] shadow-[0_10px_22px_rgba(88,44,14,0.08)] transition hover:border-[#ffc7a0] hover:bg-[#fff7f0] dark:border-[#4a2a1b] dark:bg-black dark:text-white dark:hover:border-[#FE6807] dark:hover:bg-[#120d0a] xl:w-auto xl:px-5">
                            <span>Categorias de ficheiro</span>
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div id="resourcesFilterDropdown"
                            class="z-10 hidden w-48 rounded-2xl border border-[#f3e4d8] bg-white p-2 text-sm text-[#6f4a33] shadow-[0_18px_36px_rgba(88,44,14,0.14)] dark:border-[#241915] dark:bg-black dark:text-white">
                            <ul aria-labelledby="resourcesFilterButton">
                                <li><a href="#" class="block rounded-xl px-4 py-2 font-medium hover:bg-[#fff4ec] dark:hover:bg-[#171717]">Todos os ficheiros</a></li>
                                <li><a href="#" class="block rounded-xl px-4 py-2 hover:bg-[#fff4ec] dark:hover:bg-[#171717]">PDF</a></li>
                                <li><a href="#" class="block rounded-xl px-4 py-2 hover:bg-[#fff4ec] dark:hover:bg-[#171717]">Video</a></li>
                                <li><a href="#" class="block rounded-xl px-4 py-2 hover:bg-[#fff4ec] dark:hover:bg-[#171717]">MP3</a></li>
                                <li><a href="#" class="block rounded-xl px-4 py-2 hover:bg-[#fff4ec] dark:hover:bg-[#171717]">Livros</a></li>
                                <li><a href="#" class="block rounded-xl px-4 py-2 hover:bg-[#fff4ec] dark:hover:bg-[#171717]">Slides</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="relative w-full">
                        <button id="resourceTypeButton" data-dropdown-toggle="resourceTypeDropdown" type="button"
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-full border border-[#ffd8bf] bg-white px-4 text-sm font-semibold text-[#7d4a2b] shadow-[0_10px_22px_rgba(88,44,14,0.08)] transition hover:border-[#ffc7a0] hover:bg-[#fff7f0] dark:border-[#4a2a1b] dark:bg-black dark:text-white dark:hover:border-[#FE6807] dark:hover:bg-[#120d0a] xl:w-auto xl:px-5">
                            <span>Tipo de recurso</span>
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div id="resourceTypeDropdown"
                            class="z-10 hidden w-44 rounded-2xl border border-[#f3e4d8] bg-white p-2 text-sm text-[#6f4a33] shadow-[0_18px_36px_rgba(88,44,14,0.14)] dark:border-[#241915] dark:bg-black dark:text-white">
                            <ul aria-labelledby="resourceTypeButton">
                                <li><a href="#"
                                        class="block rounded-xl px-4 py-2 font-medium hover:bg-[#fff4ec] dark:hover:bg-[#171717]">Todos</a></li>
                                <li><a href="#" class="block rounded-xl px-4 py-2 hover:bg-[#fff4ec] dark:hover:bg-[#171717]">Fisicos</a></li>
                                <li><a href="#" class="block rounded-xl px-4 py-2 hover:bg-[#fff4ec] dark:hover:bg-[#171717]">Digitais</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative mt-6 grid items-start gap-5 sm:gap-6 xl:grid-cols-2 xl:gap-8">
                @foreach ($resourceCards as $resource)
                    <article
                        class="group relative flex flex-col overflow-hidden rounded-[20px] border border-[#f6e3d3] bg-[linear-gradient(180deg,#ffffff_0%,#fff8f1_100%)] p-3 shadow-[0_18px_34px_rgba(88,44,14,0.08)] transition duration-200 hover:-translate-y-1 hover:border-[#ffd1b0] hover:shadow-[0_24px_42px_rgba(254,104,7,0.14)] dark:border-[#241915] dark:bg-[linear-gradient(180deg,#0b0b0b_0%,#050505_100%)] dark:shadow-[0_18px_34px_rgba(0,0,0,0.34)] dark:hover:border-[#FE6807]/50 sm:rounded-[24px] md:flex-row md:gap-4">
                        <div
                            class="pointer-events-none absolute right-0 top-0 h-28 w-28 rounded-full bg-[#FE6807]/8 blur-3xl">
                        </div>

                        <div
                            class="relative h-40 overflow-hidden rounded-[18px] bg-gradient-to-br {{ $resource['cover_class'] }} p-4 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] sm:h-44 md:h-auto md:min-h-60 md:w-36 md:flex-none lg:w-40">
                            <div class="absolute inset-x-4 top-4 h-px bg-white/30"></div>
                            <div class="absolute -right-6 top-8 h-20 w-20 rounded-full bg-white/10 blur-2xl"></div>
                            <div class="absolute bottom-4 left-4 h-12 w-12 rounded-full border border-white/20"></div>
                            <div class="absolute inset-x-0 bottom-0 h-14 bg-gradient-to-t from-black/20 to-transparent">
                            </div>

                            <div class="relative flex h-full flex-col justify-between">
                                <div class="flex items-start justify-between gap-3 md:flex-col md:gap-2 xl:flex-row">
                                    <span
                                        class="rounded-full bg-white/15 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.32em] text-white/90 backdrop-blur">
                                        {{ $resource['tag'] }}
                                    </span>

                                    <span
                                        class="rounded-full border border-white/20 bg-black/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-white/75">
                                        {{ $resource['category_label'] }}
                                    </span>
                                </div>

                                <div>
                                    <p
                                        class="whitespace-pre-line text-[1.1rem] font-semibold leading-[1.15] tracking-tight sm:text-[1.25rem]">
                                        {{ $resource['cover_title'] }}
                                    </p>
                                    <p class="mt-3 text-[10px] uppercase tracking-[0.28em] text-white/75">
                                        {{ $resource['cover_author'] }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="relative flex flex-1 flex-col gap-4 px-1 pb-1 pt-4 md:min-w-0 md:pt-1">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h2 class="break-words text-lg font-semibold leading-tight text-[#2c1c13] dark:text-white sm:text-xl">
                                            {{ $resource['title'] }}
                                        </h2>
                                        <p class="mt-1 text-sm font-medium uppercase tracking-[0.14em] text-[#FE6807]">
                                            {{ $resource['type_label'] }}
                                        </p>
                                    </div>

                                    <span
                                        class="rounded-full border border-[#ffd8bf] bg-[#fff1e6] px-3 py-1 text-xs font-semibold text-[#c25d18] dark:border-[#4a2a1b] dark:bg-[#120d0a] dark:text-[#ffb07a]">
                                        Popular
                                    </span>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-1.5 sm:gap-2">
                                    <span
                                        class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] shadow-[0_6px_18px_rgba(88,44,14,0.06)] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">
                                        Tipo de ficheiro: {{ $resource['file_type_label'] }}
                                    </span>
                                    <span
                                        class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] shadow-[0_6px_18px_rgba(88,44,14,0.06)] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">
                                        Categoria: {{ $resource['category_label'] }}
                                    </span>
                                    <span
                                        class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] shadow-[0_6px_18px_rgba(88,44,14,0.06)] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">
                                        Recurso: {{ $resource['resource_kind_label'] }}
                                    </span>
                                    <span
                                        class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] shadow-[0_6px_18px_rgba(88,44,14,0.06)] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">
                                        {{ $resource['meta'] }}
                                    </span>
                                    <span
                                        class="rounded-full bg-[#fff7f0] px-3 py-1 text-xs font-semibold text-[#FE6807] ring-1 ring-[#ffe0c8] dark:bg-[#120d0a] dark:ring-[#4a2a1b]">
                                        {{ $resource['status'] }}
                                    </span>
                                </div>

                                <p class="mt-3 text-sm leading-6 text-[#7a5c4a] dark:text-[#d5c7be] md:line-clamp-3">
                                    {{ $resource['description'] }}
                                </p>
                            </div>

                            <div class="border-t border-[#f3e4d8] pt-3 dark:border-[#241915]">
                                <button type="button"
                                    class="inline-flex min-h-11 w-full items-center justify-center rounded-full border border-[#ffd2b2] bg-[#fff4ec] px-5 text-sm font-semibold text-[#b85214] shadow-[0_8px_18px_rgba(88,44,14,0.08)] transition hover:border-[#f7b184] hover:bg-[#ffe8d8] hover:text-[#8f3f0f] dark:border-[#4a2a1b] dark:bg-black dark:text-[#ffb07a] dark:hover:border-[#FE6807] dark:hover:bg-[#120d0a] sm:min-h-12 sm:px-6">
                                    Acessar recurso
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>

</html>
