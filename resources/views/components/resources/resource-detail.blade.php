@props(['resource'])

@guest
    <main class="item bg-white px-3 pb-10 pt-28 dark:bg-black sm:px-5 md:px-6 lg:px-8">
        <section
            class="mx-auto max-w-3xl rounded-2xl border border-[#f6e3d3] bg-white p-6 text-center shadow-[0_24px_54px_rgba(88,44,14,0.10)] dark:border-[#241915] dark:bg-[#050505]">
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-[#c97d46]">Acesso restrito</p>
            <h1 class="mt-3 text-2xl font-semibold text-[#2c1c13] dark:text-white">Entre para visualizar este recurso</h1>
            <p class="mt-3 text-sm leading-6 text-[#7a5c4a] dark:text-[#d5c7be]">
                Os detalhes completos da biblioteca ficam disponiveis apenas para utilizadores autenticados.
            </p>
            <a href="{{ route('login') }}"
                class="mt-6 inline-flex min-h-11 items-center justify-center rounded-full bg-[#FE6807] px-6 text-sm font-semibold text-white transition hover:bg-[#e15f07]">
                Entrar
            </a>
        </section>
    </main>
@else
    @php
        $isDigital = $resource->type === 'digital';
        $digital = $resource->digitalResource;
        $physical = $resource->physicalResource;
        $filePath = $digital?->file_path;
        $fileUrl = $filePath ? \Illuminate\Support\Facades\Storage::url($filePath) : null;
        $extension = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;
        $accessType = $digital?->access_type;
        $isViewMode = $isDigital && $accessType === 'view' && $fileUrl;
        $isDownloadMode = $isDigital && $accessType === 'download' && $fileUrl;
        $isPdf = $extension === 'pdf';
        $isVideo = in_array($extension, ['mp4', 'mov', 'webm'], true);
        $isSlide = in_array($extension, ['ppt', 'pptx'], true);
        $maxLoanDays = (int) ($physical?->max_loan_days ?? 7);
        $loanStartDate = now()->toDateString();
        $loanEndDate = now()->addDays($maxLoanDays)->toDateString();

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

        $statusLabels = [
            'available' => 'Disponivel',
            'reserved' => 'Reservado',
            'active' => 'Em uso',
        ];

        $accessTypeLabels = [
            'download' => 'Download',
            'view' => 'Visualizacao',
        ];

        $coverClasses = [
            'from-[#2a1813] via-[#5d3528] to-[#ba9872]',
            'from-[#5b2b75] via-[#8c5ac9] to-[#f1cf61]',
            'from-[#8eb7d8] via-[#cfe3ef] to-[#f6edd7]',
            'from-[#205b8c] via-[#4f99db] to-[#f3b84b]',
            'from-[#3b6f8b] via-[#8ab6c8] to-[#f0db92]',
            'from-[#1c1c1c] via-[#434343] to-[#c8a76a]',
        ];

        $coverClass = $coverClasses[$resource->id % count($coverClasses)];
        $titleWords = preg_split('/\s+/', trim($resource->title));
        $coverTitle = wordwrap(strtoupper(collect($titleWords)->take(4)->implode(' ')), 13, "\n", true);
        $fileType = $isDigital ? ($fileTypeLabels[$extension] ?? strtoupper($extension ?: 'Digital')) : 'Livro';
        $status = $statusLabels[$resource->status] ?? ucfirst((string) $resource->status);
        $resourceType = $isDigital ? 'Recurso digital' : 'Recurso fisico';
        $quantity = (int) $resource->quantity_available;
        $canBorrow = ! $isDigital && $quantity > 0 && $resource->status === 'available';

        $details = $isDigital
            ? [
                'Tipo de ficheiro' => $fileType,
                'Modo de acesso' => $accessTypeLabels[$digital?->access_type] ?? ucfirst((string) $digital?->access_type ?: 'Nao definido'),
                'Dias de acesso' => $digital?->access_days ? $digital->access_days . ' dias' : 'Nao definido',
                'Caminho do ficheiro' => $filePath ?: 'Nao definido',
            ]
            : [
                'Localizacao' => $physical?->location ?: 'Nao definido',
                'Prazo maximo' => $physical?->max_loan_days ? $physical->max_loan_days . ' dias' : 'Nao definido',
                'Condicao' => $physical?->condition ?: 'Nao definido',
                'Exemplares disponiveis' => $quantity,
            ];
    @endphp

    <main class="item bg-white px-3 pb-10 pt-24 dark:bg-black sm:px-5 sm:pb-14 md:px-6 lg:px-8">
        <section
            class="relative mx-auto max-w-7xl overflow-hidden rounded-2xl border border-white/70 bg-white/90 p-4 shadow-[0_28px_70px_rgba(254,104,7,0.12)] backdrop-blur dark:border-[#241915] dark:bg-[#050505]/95 dark:shadow-[0_28px_70px_rgba(0,0,0,0.42)] sm:rounded-[28px] sm:p-5 md:p-8">
            <div class="pointer-events-none absolute -right-12 top-0 h-40 w-40 rounded-full bg-[#FE6807]/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-10 bottom-0 h-32 w-32 rounded-full bg-[#fe6807]/8 blur-3xl"></div>

            <div class="relative grid gap-6 lg:grid-cols-[minmax(260px,0.8fr)_minmax(0,1.2fr)] lg:gap-8">
                <div
                    class="relative min-h-[28rem] overflow-hidden rounded-[22px] bg-gradient-to-br {{ $coverClass }} p-5 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.24)] sm:p-6">
                    <div class="absolute inset-x-5 top-5 h-px bg-white/30"></div>
                    <div class="absolute -right-10 top-16 h-36 w-36 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="absolute bottom-8 left-6 h-20 w-20 rounded-full border border-white/20"></div>
                    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/30 to-transparent"></div>

                    <div class="relative flex h-full flex-col justify-between">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <span
                                class="rounded-full bg-white/15 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.32em] text-white/90 backdrop-blur">
                                {{ $fileType }}
                            </span>
                            <span
                                class="rounded-full border border-white/20 bg-black/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-white/75">
                                {{ $isDigital ? 'Digital' : 'Fisico' }}
                            </span>
                        </div>

                        <div>
                            <p class="whitespace-pre-line text-3xl font-semibold leading-tight tracking-tight sm:text-4xl">
                                {{ $coverTitle }}
                            </p>
                            <p class="mt-5 text-xs font-semibold uppercase tracking-[0.28em] text-white/75">
                                {{ strtoupper($resource->owner?->name ?? 'VUTIVI') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative min-w-0">
                    <a href="{{ route('library') }}"
                        class="inline-flex min-h-10 items-center justify-center rounded-full border border-[#ffd8bf] bg-[#fff4ec] px-4 text-sm font-semibold text-[#9f5627] transition hover:border-[#ffc7a0] hover:bg-[#ffe8d8] dark:border-[#4a2a1b] dark:bg-[#120d0a] dark:text-[#ffb07a]">
                        Voltar para recursos
                    </a>

                    <div class="mt-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#c97d46]">{{ $resourceType }}</p>
                        <h1 class="mt-3 break-words text-3xl font-semibold leading-tight text-[#2c1c13] dark:text-white sm:text-4xl">
                            {{ $resource->title }}
                        </h1>
                        <p class="mt-4 text-base leading-7 text-[#7a5c4a] dark:text-[#d5c7be]">
                            {{ $resource->description }}
                        </p>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span
                            class="rounded-full border border-[#ffd8bf] bg-[#fff1e6] px-3 py-1 text-xs font-semibold text-[#c25d18] dark:border-[#4a2a1b] dark:bg-[#120d0a] dark:text-[#ffb07a]">
                            {{ $status }}
                        </span>
                        <span
                            class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] shadow-[0_6px_18px_rgba(88,44,14,0.06)] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">
                            {{ $quantity }} {{ $quantity === 1 ? 'disponivel' : 'disponiveis' }}
                        </span>
                        <span
                            class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8a5d40] shadow-[0_6px_18px_rgba(88,44,14,0.06)] ring-1 ring-[#f5e1d1] dark:bg-black dark:text-[#d5c7be] dark:ring-[#241915]">
                            Dono: {{ $resource->owner?->name ?? 'Nao definido' }}
                        </span>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-2">
                        @foreach ($details as $label => $value)
                            <div
                                class="rounded-2xl border border-[#f3e4d8] bg-white p-4 shadow-[0_12px_28px_rgba(88,44,14,0.06)] dark:border-[#241915] dark:bg-black">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#c97d46]">{{ $label }}</p>
                                <p class="mt-2 break-words text-sm font-semibold text-[#2c1c13] dark:text-white">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 border-t border-[#f3e4d8] pt-5 dark:border-[#241915]">
                        @if (session('success'))
                            <div
                                class="mb-5 rounded-2xl border border-[#bfe8cd] bg-[#f0fff5] px-4 py-3 text-sm font-semibold text-[#26723b] dark:border-[#174a26] dark:bg-[#07120a] dark:text-[#9be0ad]">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($isViewMode)
                            <div
                                class="overflow-hidden rounded-2xl border border-[#f3e4d8] bg-[#fffaf6] shadow-[0_14px_30px_rgba(88,44,14,0.08)] dark:border-[#241915] dark:bg-black">
                                <div
                                    class="flex items-center justify-between gap-3 border-b border-[#f3e4d8] px-4 py-3 dark:border-[#241915]">
                                    <p class="text-sm font-semibold text-[#2c1c13] dark:text-white">Leitor de {{ $fileType }}</p>
                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener"
                                        class="inline-flex min-h-9 items-center justify-center rounded-full border border-[#ffd8bf] px-4 text-xs font-semibold text-[#9f5627] transition hover:bg-[#fff4ec] dark:border-[#4a2a1b] dark:text-[#ffb07a] dark:hover:bg-[#120d0a]">
                                        Abrir
                                    </a>
                                </div>

                                @if ($isPdf)
                                    <iframe src="{{ $fileUrl }}"
                                        class="h-[32rem] w-full bg-white dark:bg-[#080808]"
                                        title="Leitor de PDF: {{ $resource->title }}"></iframe>
                                @elseif ($isVideo)
                                    <video controls class="aspect-video w-full bg-black">
                                        <source src="{{ $fileUrl }}" type="video/{{ $extension === 'mov' ? 'quicktime' : $extension }}">
                                    </video>
                                @elseif ($isSlide)
                                    <iframe src="{{ $fileUrl }}"
                                        class="h-[32rem] w-full bg-white dark:bg-[#080808]"
                                        title="Leitor de slides: {{ $resource->title }}"></iframe>
                                @else
                                    <div class="px-4 py-8 text-sm leading-6 text-[#7a5c4a] dark:text-[#d5c7be]">
                                        Este tipo de ficheiro nao tem leitor embutido disponivel no navegador.
                                    </div>
                                @endif
                            </div>
                        @elseif ($isDownloadMode)
                            <a href="{{ $fileUrl }}" download
                                class="inline-flex min-h-12 w-full items-center justify-center rounded-full bg-[#FE6807] px-6 text-sm font-semibold text-white shadow-[0_14px_24px_rgba(254,104,7,0.22)] transition hover:bg-[#e15f07] sm:w-auto">
                                Baixar ficheiro
                            </a>
                        @elseif (! $isDigital)
                            <form method="POST" action="{{ route('reservations.store') }}"
                                class="rounded-2xl border border-[#f3e4d8] bg-white p-4 shadow-[0_12px_28px_rgba(88,44,14,0.06)] dark:border-[#241915] dark:bg-black">
                                @csrf
                                <input type="hidden" name="resource_id" value="{{ $resource->id }}">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                <input type="hidden" name="type" value="physical">

                                <div class="flex flex-col gap-4 sm:grid sm:grid-cols-2">
                                    <label class="text-sm font-semibold text-[#2c1c13] dark:text-white">
                                        Inicio do emprestimo
                                        <input type="date" name="start_date" value="{{ old('start_date', $loanStartDate) }}"
                                            min="{{ $loanStartDate }}"
                                            class="mt-2 min-h-11 w-full rounded-xl border border-[#f3e4d8] bg-[#fffaf6] px-3 text-sm text-[#2c1c13] outline-none focus:border-[#FE6807] dark:border-[#241915] dark:bg-[#050505] dark:text-white">
                                    </label>

                                    <label class="text-sm font-semibold text-[#2c1c13] dark:text-white">
                                        Fim do emprestimo
                                        <input type="date" name="end_date" value="{{ old('end_date', $loanEndDate) }}"
                                            min="{{ $loanStartDate }}"
                                            class="mt-2 min-h-11 w-full rounded-xl border border-[#f3e4d8] bg-[#fffaf6] px-3 text-sm text-[#2c1c13] outline-none focus:border-[#FE6807] dark:border-[#241915] dark:bg-[#050505] dark:text-white">
                                    </label>
                                </div>

                                @if ($errors->any())
                                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <button type="submit" @disabled(! $canBorrow)
                                    class="mt-5 inline-flex min-h-12 w-full items-center justify-center rounded-full bg-[#FE6807] px-6 text-sm font-semibold text-white shadow-[0_14px_24px_rgba(254,104,7,0.22)] transition hover:bg-[#e15f07] disabled:cursor-not-allowed disabled:bg-[#c9b8ad] disabled:shadow-none sm:w-auto">
                                    {{ $canBorrow ? 'Solicitar emprestimo do livro' : 'Sem disponibilidade para emprestimo' }}
                                </button>
                            </form>
                        @else
                            <div
                                class="rounded-2xl border border-[#f3e4d8] bg-white px-4 py-5 text-sm font-semibold text-[#7a5c4a] dark:border-[#241915] dark:bg-black dark:text-[#d5c7be]">
                                Ficheiro indisponivel para acesso neste momento.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
@endguest
