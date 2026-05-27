@props(['resource'])

@php
    $accessUrl = $resource['show_route'] ?? route('resources.show', $resource['id']);
    $isDigital = ($resource['type'] ?? null) === 'digital';
    $isAvailable = str_contains(strtolower($resource['status'] ?? ''), 'dispon');
    $favoriteLabel = ($resource['is_favorited'] ?? false) ? 'Favorito' : 'Favoritar';
    $moderationStatus = $resource['moderation_status'] ?? 'approved';
    $moderationLabels = [
        'review' => 'Recurso em analise',
        'approved' => 'Aprovado',
        'rejected' => 'Recurso recusado',
    ];
@endphp

<article data-resource-card data-resource-id="{{ $resource['id'] }}"
    class="group relative flex flex-col overflow-hidden rounded-xl border border-[#eadfce] bg-white p-2 shadow-[0_16px_34px_rgba(54,39,25,0.07)] transition duration-300 hover:-translate-y-1 hover:border-[#d8c5ad] hover:shadow-[0_24px_52px_rgba(54,39,25,0.12)] dark:border-[#27211a] dark:bg-[#090909] dark:shadow-[0_18px_34px_rgba(0,0,0,0.30)] dark:hover:border-[#534337] md:flex-row md:gap-4 md:p-3">

    <div class="relative h-48 overflow-hidden rounded-lg bg-gradient-to-br {{ $resource['cover_class'] }} p-3 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] sm:h-52 md:h-auto md:min-h-60 md:w-36 md:flex-none md:p-4 lg:w-40">
        @if (! empty($resource['cover_image']))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($resource['cover_image']) }}" alt="Capa de {{ $resource['title'] }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-black/18 to-black/20"></div>
        @endif

        <div class="relative flex h-full flex-col justify-between">
            <div class="flex items-start justify-between gap-3 md:flex-col md:gap-2 xl:flex-row">
                <span class="rounded-full bg-white/15 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-[0.32em] text-white/90 backdrop-blur md:px-3 md:py-1 md:text-[10px]">{{ $resource['tag'] }}</span>
                <span class="rounded-full border border-white/20 bg-black/10 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-[0.25em] text-white/75 md:px-3 md:py-1 md:text-[10px]">{{ $resource['category_label'] }}</span>
            </div>

            <div>
                <p class="whitespace-pre-line text-[0.95rem] font-semibold leading-[1.15] tracking-tight sm:text-[1rem] md:text-[1.1rem] md:sm:text-[1.25rem]">{{ $resource['cover_title'] }}</p>
                <p class="mt-2 text-[9px] uppercase tracking-[0.28em] text-white/75 md:mt-3 md:text-[10px]">{{ $resource['cover_author'] }}</p>
            </div>
        </div>
    </div>

    <div class="relative flex flex-1 flex-col gap-3 px-1 pb-1 pt-3 md:min-w-0 md:gap-4 md:pt-1">
        <div class="flex-1">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="break-words text-lg font-semibold leading-tight text-[#2c1c13] dark:text-white sm:text-xl">{{ $resource['title'] }}</h2>
                    <p class="mt-1 text-sm font-medium uppercase tracking-[0.14em] text-[#FE6807]">{{ $resource['type_label'] }}</p>
                </div>

                <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $isAvailable ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/20 dark:text-emerald-200' : 'border-[#d8c5ad] bg-[#f8f0e8] text-[#6a4d38] dark:border-[#332820] dark:bg-[#15120f] dark:text-[#d8cec3]' }}">
                    {{ $isDigital ? 'Acesso digital' : (($resource['available_count'] ?? 0) . ' disponíveis') }}
                </span>
            </div>

        <div class="flex flex-wrap gap-1.5 sm:gap-2">
                @foreach ([
                    'Tipo de ficheiro: ' . $resource['file_type_label'],
                    'Categoria: ' . $resource['category_label'],
                    'Recurso: ' . $resource['resource_kind_label'],
                    'Autor: ' . ($resource['authors'] ?? 'Nao definido'),
                    $resource['meta'],
                    'Dono: ' . ($resource['owner'] ?? 'Não definido'),
                ] as $chip)
                    @if (str_starts_with($chip, 'Categoria:') || str_starts_with($chip, 'Recurso:') || str_starts_with($chip, 'Dono:') || str_contains($chip, 'dias de empr'))
                        @continue
                    @endif
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] shadow-[0_6px_18px_rgba(88,44,14,0.06)] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">{{ $chip }}</span>
                @endforeach
                <span class="rounded-full bg-[#fff7f0] px-3 py-1 text-xs font-semibold text-[#FE6807] ring-1 ring-[#ffe0c8] dark:bg-[#120d0a] dark:ring-[#4a2a1b]">{{ $resource['status'] }}</span>
                @if (in_array($moderationStatus, ['review', 'rejected'], true))
                    <span class="rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $moderationStatus === 'rejected' ? 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-950/30 dark:text-red-200 dark:ring-red-900/50' : 'bg-amber-50 text-amber-800 ring-amber-200 dark:bg-amber-950/30 dark:text-amber-100 dark:ring-amber-900/50' }}" title="{{ $resource['moderation_reason'] ?? '' }}">
                        {{ $moderationLabels[$moderationStatus] ?? $moderationStatus }}
                    </span>
                @endif
            </div>

            <div class="mt-4 hidden grid-cols-3 gap-2">
                <div class="rounded-xl border border-[#f1dfcd] bg-[#fffaf5] px-3 py-2 dark:border-[#2f241d] dark:bg-[#0d0b09]">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#8a6a4d] dark:text-[#bcae9f]">Downloads</p>
                    <p class="mt-1 flex items-center gap-1 text-sm font-black text-[#241b14] dark:text-white">
                        <svg class="h-3.5 w-3.5 text-[#FE6807]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
                        <span data-download-count>{{ $resource['downloads_count'] ?? 0 }}</span>
                    </p>
                </div>
                <div class="rounded-xl border border-[#f1dfcd] bg-[#fffaf5] px-3 py-2 dark:border-[#2f241d] dark:bg-[#0d0b09]">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#8a6a4d] dark:text-[#bcae9f]">Favoritos</p>
                    <p class="mt-1 flex items-center gap-1 text-sm font-black text-[#241b14] dark:text-white">
                        <svg data-favorite-icon class="h-3.5 w-3.5 text-[#FE6807] transition" viewBox="0 0 24 24" fill="{{ ($resource['is_favorited'] ?? false) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2"><path d="m12 21-1.45-1.32C5.4 15.02 2 11.93 2 8.15 2 5.06 4.42 3 7.5 3c1.74 0 3.41.8 4.5 2.05A6.02 6.02 0 0 1 16.5 3C19.58 3 22 5.06 22 8.15c0 3.78-3.4 6.87-8.55 11.53L12 21Z"/></svg>
                        <span data-favorite-count>{{ $resource['favorites_count'] ?? 0 }}</span>
                    </p>
                </div>
                <div class="rounded-xl border border-[#f1dfcd] bg-[#fffaf5] px-3 py-2 dark:border-[#2f241d] dark:bg-[#0d0b09]">
                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#8a6a4d] dark:text-[#bcae9f]">Formato</p>
                    <p class="mt-1 text-sm font-black {{ $isDigital ? 'text-sky-700 dark:text-sky-200' : 'text-amber-700 dark:text-amber-200' }}">{{ $isDigital ? 'Digital' : 'Físico' }}</p>
                </div>
            </div>

            <p class="mt-3 text-sm leading-6 text-[#7a5c4a] dark:text-[#d5c7be] md:line-clamp-2">{{ $resource['description'] }}</p>
        </div>

        <div class="flex flex-col gap-2 border-t border-[#f3e4d8] pt-2 dark:border-[#241915] sm:flex-row sm:gap-1.5">
            <a href="{{ $accessUrl }}" class="inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-lg border border-[#ffd2b2] bg-[#fff4ec] px-3 text-xs font-semibold text-[#b85214] shadow-[0_8px_18px_rgba(88,44,14,0.08)] transition hover:border-[#f7b184] hover:bg-[#ffe8d8] hover:text-[#8f3f0f] dark:border-[#4a2a1b] dark:bg-black dark:text-[#ffb07a] dark:hover:border-[#FE6807] dark:hover:bg-[#120d0a] sm:text-sm sm:px-5 sm:min-h-11">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                Ver mais
            </a>

            @auth
                <form data-favorite-form method="POST" action="{{ $resource['favorite_route'] }}">
                    @csrf
                    <button data-favorite-button class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-[#decbb8] bg-white px-3 text-xs font-bold text-[#5f4632] transition hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#332820] dark:bg-[#050505] dark:text-[#d8cec3] sm:text-sm sm:px-4 sm:min-h-11" title="{{ $favoriteLabel }}">
                        <svg data-favorite-icon class="h-4 w-4 text-[#FE6807] transition" viewBox="0 0 24 24" fill="{{ ($resource['is_favorited'] ?? false) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2"><path d="m12 21-1.45-1.32C5.4 15.02 2 11.93 2 8.15 2 5.06 4.42 3 7.5 3c1.74 0 3.41.8 4.5 2.05A6.02 6.02 0 0 1 16.5 3C19.58 3 22 5.06 22 8.15c0 3.78-3.4 6.87-8.55 11.53L12 21Z"/></svg>
                        <span data-favorite-label>{{ $favoriteLabel }}</span>
                    </button>
                </form>
            @endauth

            <div class="relative">
                <button data-dropdown-toggle="resource-actions-{{ $resource['id'] }}" data-dropdown-placement="bottom-end" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-[#decbb8] bg-white px-3 text-[#5f4632] transition hover:bg-[#fff7f0] dark:border-[#332820] dark:bg-[#050505] dark:text-[#d8cec3] dark:hover:bg-[#15120f] sm:text-sm sm:px-4 sm:min-h-11" title="Mais ações">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                </button>
                <div id="resource-actions-{{ $resource['id'] }}" class="z-40 hidden w-56 rounded-2xl border border-[#eadfce] bg-white p-2 shadow-[0_18px_48px_rgba(54,39,25,0.14)] dark:border-[#2b211b] dark:bg-[#0b0908]">
                    <button type="button" data-copy-link data-copy-link="{{ $accessUrl }}" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm font-bold text-[#5f4632] hover:bg-[#fff7f0] dark:text-[#d8cec3] dark:hover:bg-[#15110e]">
                        <span data-copy-label>Copiar link</span>
                    </button>
                    @if (($resource['show_owner_actions'] ?? false) && ($resource['can_manage'] ?? false))
                        <a href="{{ $resource['edit_route'] }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-bold text-[#5f4632] hover:bg-[#fff7f0] dark:text-[#d8cec3] dark:hover:bg-[#15110e]">Editar</a>
                        <form method="POST" action="{{ $resource['delete_route'] }}" onsubmit="return confirm('Remover este recurso da biblioteca?')">
                            @csrf
                            @method('DELETE')
                            <button class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm font-bold text-red-700 hover:bg-red-50 dark:text-red-200 dark:hover:bg-red-950/30">Remover</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</article>
