@props(['resource'])

@guest
    <main class="item bg-white px-3 pb-10 pt-28 dark:bg-black sm:px-5 md:px-6 lg:px-8">
        <section class="mx-auto max-w-3xl rounded-2xl border border-[#f6e3d3] bg-white p-6 text-center shadow-[0_24px_54px_rgba(88,44,14,0.10)] dark:border-[#241915] dark:bg-[#050505]">
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-[#c97d46]">Acesso restrito</p>
            <h1 class="mt-3 text-2xl font-semibold text-[#2c1c13] dark:text-white">Entre para visualizar este recurso</h1>
            <a href="{{ route('login') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-full bg-[#FE6807] px-6 text-sm font-semibold text-white transition hover:bg-[#e15f07]">Entrar</a>
        </section>
    </main>
@else
    @php
        $isDigital = $resource->type === 'digital';
        $digital = $resource->digitalResource;
        $physical = $resource->physicalResource;
        $filePath = $digital?->file_path;
        $extension = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;
        $accessType = $digital?->access_type;
        $fileUrl = $isDigital && $filePath
            ? ($accessType === 'download' ? route('resources.digital.download', $resource->id) : route('resources.digital.view', $resource->id))
            : null;
        $isViewMode = $isDigital && $accessType === 'view' && $fileUrl;
        $isDownloadMode = $isDigital && $accessType === 'download' && $fileUrl;
        $isPdf = $extension === 'pdf';
        $isVideo = in_array($extension, ['mp4', 'mov', 'webm'], true);
        $isSlide = in_array($extension, ['ppt', 'pptx'], true);
        $maxLoanDays = (int) ($physical?->max_loan_days ?? 7);
        $quantity = (int) $resource->quantity_available;
        $canBorrow = ! $isDigital && $quantity > 0 && (int) $resource->owner_id !== (int) auth()->id();
        $isOwnResource = (int) $resource->owner_id === (int) auth()->id();
        $shareUrl = $resource->slug ? route('resources.public.show', $resource->slug) : route('resources.show', $resource->id);

        $fileTypeLabels = [
            'pdf' => 'PDF', 'mp4' => 'Vídeo', 'mov' => 'Vídeo', 'webm' => 'Vídeo', 'mp3' => 'Áudio',
            'ppt' => 'Slides', 'pptx' => 'Slides', 'doc' => 'Documento', 'docx' => 'Documento',
            'xls' => 'Planilha', 'xlsx' => 'Planilha', 'zip' => 'Arquivo',
        ];
        $statusLabels = ['available' => 'Disponível', 'reserved' => 'Indisponível', 'active' => $quantity > 0 ? 'Disponível' : 'Indisponível'];
        $accessTypeLabels = ['download' => 'Download permitido', 'view' => 'Somente visualização'];
        $fileType = $isDigital ? ($fileTypeLabels[$extension] ?? 'Digital') : 'Livro';
        $status = $statusLabels[$resource->status] ?? ucfirst((string) $resource->status);
        $coverClasses = [
            'from-[#2a1813] via-[#5d3528] to-[#ba9872]',
            'from-[#205b8c] via-[#4f99db] to-[#f3b84b]',
            'from-[#3b6f8b] via-[#8ab6c8] to-[#f0db92]',
            'from-[#1c1c1c] via-[#434343] to-[#c8a76a]',
        ];
        $coverClass = $coverClasses[$resource->id % count($coverClasses)];
        $coverTitle = wordwrap(strtoupper(collect(preg_split('/\s+/', trim($resource->title)))->take(4)->implode(' ')), 13, "\n", true);
        $details = $isDigital
            ? [
                'Tipo de ficheiro' => $fileType,
                'Permissão' => $accessTypeLabels[$accessType] ?? 'Não definido',
                'Janela de acesso' => $digital?->access_days ? $digital->access_days . ' dias' : 'Não definido',
            ]
            : [
                'Localização' => $physical?->location ?: 'Não definido',
                'Prazo máximo' => $physical?->max_loan_days ? $physical->max_loan_days . ' dias' : 'Não definido',
                'Condição' => $physical?->condition ?: 'Não definido',
                'Cópias disponíveis' => $quantity,
            ];
    @endphp

    <main class="item bg-white px-3 pb-10 pt-24 dark:bg-black sm:px-5 sm:pb-14 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Biblioteca', 'url' => route('library')], ['label' => $isDigital ? 'Recursos digitais' : 'Recursos físicos', 'url' => route('library', ['type' => $resource->type])], ['label' => $resource->title]]" />

        <section class="relative mx-auto max-w-7xl overflow-hidden rounded-2xl border border-white/70 bg-white/90 p-4 shadow-[0_28px_70px_rgba(254,104,7,0.12)] backdrop-blur dark:border-[#241915] dark:bg-[#050505]/95 sm:rounded-[28px] sm:p-5 md:p-8">
            <div class="relative grid gap-6 lg:grid-cols-[minmax(260px,0.8fr)_minmax(0,1.2fr)] lg:gap-8">
                <div class="relative min-h-[28rem] overflow-hidden rounded-[22px] bg-gradient-to-br {{ $coverClass }} p-5 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] sm:p-6">
                    @if ($resource->cover_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($resource->cover_image) }}" alt="Capa de {{ $resource->title }}" class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-black/20"></div>
                    @endif
                    <div class="relative flex h-full flex-col justify-between">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <span class="rounded-full bg-white/15 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.32em] text-white/90 backdrop-blur">{{ $fileType }}</span>
                            <span class="rounded-full border border-white/20 bg-black/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-white/75">{{ $isDigital ? 'Digital' : 'Físico' }}</span>
                        </div>
                        <div>
                            <p class="whitespace-pre-line text-3xl font-semibold leading-tight tracking-tight sm:text-4xl">{{ $coverTitle }}</p>
                            <p class="mt-5 text-xs font-semibold uppercase tracking-[0.28em] text-white/75">{{ strtoupper($resource->owner?->name ?? 'VUTIVI') }}</p>
                        </div>
                    </div>
                </div>

                <div class="relative min-w-0">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('library') }}" class="inline-flex min-h-10 items-center justify-center rounded-full border border-[#ffd8bf] bg-[#fff4ec] px-4 text-sm font-semibold text-[#9f5627] transition hover:border-[#ffc7a0] hover:bg-[#ffe8d8] dark:border-[#4a2a1b] dark:bg-[#120d0a] dark:text-[#ffb07a]">Voltar para recursos</a>
                        <button type="button" data-copy-link data-copy-link="{{ $shareUrl }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-full border border-[#decbb8] bg-white px-4 text-sm font-semibold text-[#5f4632] hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#332820] dark:bg-[#0d0b09] dark:text-[#d8cec3]">
                            <span data-copy-label>Copiar link</span>
                        </button>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#c97d46]">{{ $isDigital ? 'Recurso digital' : 'Recurso físico' }}</p>
                        <h1 class="mt-3 break-words text-3xl font-semibold leading-tight text-[#2c1c13] dark:text-white sm:text-4xl">{{ $resource->title }}</h1>
                        <p class="mt-4 text-base leading-7 text-[#7a5c4a] dark:text-[#d5c7be]">{{ $resource->description }}</p>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="rounded-full border border-[#ffd8bf] bg-[#fff1e6] px-3 py-1 text-xs font-semibold text-[#c25d18] dark:border-[#4a2a1b] dark:bg-[#120d0a] dark:text-[#ffb07a]">{{ $status }}</span>
                        @unless ($isDigital)
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">{{ $quantity }} {{ $quantity === 1 ? 'cópia disponível' : 'cópias disponíveis' }}</span>
                        @endunless
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">Dono: {{ $resource->owner?->name ?? 'Não definido' }}</span>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-2">
                        @foreach ($details as $label => $value)
                            <div class="rounded-2xl border border-[#f3e4d8] bg-white p-4 shadow-[0_12px_28px_rgba(88,44,14,0.06)] dark:border-[#241915] dark:bg-black">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#c97d46]">{{ $label }}</p>
                                <p class="mt-2 break-words text-sm font-semibold text-[#2c1c13] dark:text-white">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 border-t border-[#f3e4d8] pt-5 dark:border-[#241915]">
                        @if (session('success') || session('error'))
                            <div class="mb-5 rounded-2xl border px-4 py-3 text-sm font-semibold {{ session('error') ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-100' : 'border-[#bfe8cd] bg-[#f0fff5] text-[#26723b] dark:border-[#174a26] dark:bg-[#07120a] dark:text-[#9be0ad]' }}">
                                {{ session('success') ?? session('error') }}
                            </div>
                        @endif

                        @if ($isViewMode)
                            <div class="overflow-hidden rounded-2xl border border-[#f3e4d8] bg-[#fffaf6] shadow-[0_14px_30px_rgba(88,44,14,0.08)] dark:border-[#241915] dark:bg-black">
                                <div class="flex items-center justify-between gap-3 border-b border-[#f3e4d8] px-4 py-3 dark:border-[#241915]">
                                    <p class="text-sm font-semibold text-[#2c1c13] dark:text-white">Leitor de {{ $fileType }}</p>
                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="inline-flex min-h-9 items-center justify-center rounded-full border border-[#ffd8bf] px-4 text-xs font-semibold text-[#9f5627] transition hover:bg-[#fff4ec] dark:border-[#4a2a1b] dark:text-[#ffb07a]">Abrir</a>
                                </div>
                                @if ($isPdf || $isSlide)
                                    <iframe src="{{ $fileUrl }}" class="h-[32rem] w-full bg-white dark:bg-[#080808]" title="Leitor: {{ $resource->title }}"></iframe>
                                @elseif ($isVideo)
                                    <video controls class="aspect-video w-full bg-black"><source src="{{ $fileUrl }}" type="video/{{ $extension === 'mov' ? 'quicktime' : $extension }}"></video>
                                @else
                                    <div class="px-4 py-8 text-sm leading-6 text-[#7a5c4a] dark:text-[#d5c7be]">Este tipo de ficheiro não tem leitor embutido disponível no navegador.</div>
                                @endif
                            </div>
                        @elseif ($isDownloadMode)
                            <a href="{{ $fileUrl }}" download data-download-action class="inline-flex min-h-12 w-full items-center justify-center rounded-full bg-[#FE6807] px-6 text-sm font-semibold text-white shadow-[0_14px_24px_rgba(254,104,7,0.22)] transition hover:bg-[#e15f07] sm:w-auto">Baixar ficheiro</a>
                        @elseif (! $isDigital)
                            <div class="rounded-2xl border border-[#f3e4d8] bg-white p-4 shadow-[0_12px_28px_rgba(88,44,14,0.06)] dark:border-[#241915] dark:bg-black">
                                <p class="text-sm leading-6 text-[#7a5c4a] dark:text-[#d5c7be]">Para recursos físicos, o pedido passa primeiro pelo aceite dos termos e condições. O prazo máximo deste item é de {{ $maxLoanDays }} dias.</p>
                                @if ($isOwnResource)
                                    <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-100">Este recurso pertence a si. Não é possível solicitar empréstimo do próprio recurso.</p>
                                @elseif (! $canBorrow)
                                    <p class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-100">Sem cópias disponíveis.</p>
                                @endif
                                <a href="{{ $canBorrow ? route('reservations.terms.show', $resource->id) : '#' }}" class="mt-5 inline-flex min-h-12 w-full items-center justify-center rounded-full {{ $canBorrow ? 'bg-[#FE6807] text-white hover:bg-[#e15f07]' : 'pointer-events-none bg-[#eadfce] text-[#7f6652] dark:bg-[#241915] dark:text-[#bcae9f]' }} px-6 text-sm font-semibold shadow-[0_14px_24px_rgba(254,104,7,0.14)] transition sm:w-auto">
                                    {{ $canBorrow ? 'Ver termos e solicitar empréstimo' : 'Empréstimo indisponível' }}
                                </a>
                            </div>
                        @else
                            <div class="rounded-2xl border border-[#f3e4d8] bg-white px-4 py-5 text-sm font-semibold text-[#7a5c4a] dark:border-[#241915] dark:bg-black dark:text-[#d5c7be]">Ficheiro indisponível para acesso neste momento.</div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
@endguest
