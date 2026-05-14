@props(['resource'])

@php
    $accessUrl = auth()->check() ? route('resources.show', $resource['id']) : route('login');
@endphp

<article
    class="group relative flex flex-col overflow-hidden rounded-[20px] border border-[#f6e3d3] bg-[linear-gradient(180deg,#ffffff_0%,#fff8f1_100%)] p-3 shadow-[0_18px_34px_rgba(88,44,14,0.08)] transition duration-200 hover:-translate-y-1 hover:border-[#ffd1b0] hover:shadow-[0_24px_42px_rgba(254,104,7,0.14)] dark:border-[#241915] dark:bg-[linear-gradient(180deg,#0b0b0b_0%,#050505_100%)] dark:shadow-[0_18px_34px_rgba(0,0,0,0.34)] dark:hover:border-[#FE6807]/50 sm:rounded-[24px] md:flex-row md:gap-4">
    <div class="pointer-events-none absolute right-0 top-0 h-28 w-28 rounded-full bg-[#FE6807]/8 blur-3xl"></div>

    <div
        class="relative h-40 overflow-hidden rounded-[18px] bg-gradient-to-br {{ $resource['cover_class'] }} p-4 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] sm:h-44 md:h-auto md:min-h-60 md:w-36 md:flex-none lg:w-40">
        <div class="absolute inset-x-4 top-4 h-px bg-white/30"></div>
        <div class="absolute -right-6 top-8 h-20 w-20 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute bottom-4 left-4 h-12 w-12 rounded-full border border-white/20"></div>
        <div class="absolute inset-x-0 bottom-0 h-14 bg-gradient-to-t from-black/20 to-transparent"></div>

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
                    <h2
                        class="break-words text-lg font-semibold leading-tight text-[#2c1c13] dark:text-white sm:text-xl">
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
            <a href="{{ $accessUrl }}"
                class="inline-flex min-h-11 w-full items-center justify-center rounded-full border border-[#ffd2b2] bg-[#fff4ec] px-5 text-sm font-semibold text-[#b85214] shadow-[0_8px_18px_rgba(88,44,14,0.08)] transition hover:border-[#f7b184] hover:bg-[#ffe8d8] hover:text-[#8f3f0f] dark:border-[#4a2a1b] dark:bg-black dark:text-[#ffb07a] dark:hover:border-[#FE6807] dark:hover:bg-[#120d0a] sm:min-h-12 sm:px-6">
                Acessar recurso
            </a>
        </div>
    </div>
</article>