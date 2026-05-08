<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Recursos Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <!-- ─── SIDEBAR ─── -->
    <x-sidebar />

    <!-- ─── MAIN ─── -->
    <div class="main">
        <!-- TOPBAR -->
        <x-topbar />

        <!-- CONTENT -->
        <main class="content">
            <div class="content-header">
                <div>
                    <h1 class="content-title">{{ $pageTitle ?? 'Biblioteca de Recursos' }}</h1>
                </div>
                <div class="content-actions">
                    @if(request()->routeIs('library'))
                        <div class="add-resource-dropdown">
                            <button class="add-resource-btn" id="addResourceBtn">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Adicionar Recurso
                            </button>
                            <div class="add-resource-menu" id="addResourceMenu">
                                <a href="{{ route('digital-resources.create') }}" class="add-resource-option">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg>
                                    Recurso Digital
                                </a>
                                <a href="{{ route('physical-resources.create') }}" class="add-resource-option">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Recurso Físico
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="filter-dropdown">
                        <button class="filter-btn" id="filterBtn">
                            <span id="filterText">Todas as Categorias</span>
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </button>
                        <div class="filter-dropdown-menu" id="filterMenu">
                            <div class="filter-option active" data-filter="all">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Todas as Categorias
                            </div>
                            <div class="filter-option" data-filter="pdf">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                                Documentos PDF
                            </div>
                            <div class="filter-option" data-filter="doc">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                                Documentos Word
                            </div>
                            <div class="filter-option" data-filter="pptx">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2" />
                                </svg>
                                Apresentações
                            </div>
                            <div class="filter-option" data-filter="video">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <polygon points="23 7 16 12 23 17 23 7" />
                                    <rect x="1" y="5" width="15" height="14" rx="2" />
                                </svg>
                                Vídeos
                            </div>
                            <div class="filter-option" data-filter="xlsx">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <line x1="3" y1="9" x2="21" y2="9" />
                                    <line x1="3" y1="15" x2="21" y2="15" />
                                </svg>
                                Planilhas
                            </div>
                            <div class="filter-option" data-filter="zip">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                </svg>
                                Arquivos Compactados
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="resources-grid" id="resourcesGrid">

                @if(!empty($resources))
                    @forelse ($resources as $resource)
                        @php
                            $dataType = 'physical';
                            $ext = 'FIS';
                            $digitalResource = data_get($resource, 'digitalResource');
                            $physicalResource = data_get($resource, 'physicalResource');
                            $owner = data_get($resource, 'owner');
                            $statusValue = data_get($resource, 'status', 'available');
                            $resourceOwnerId = data_get($resource, 'owner_id');

                            if ($digitalResource) {
                                $pathInfo = pathinfo(data_get($digitalResource, 'file_path', '')); 
                                $dataType = strtolower($pathInfo['extension'] ?? 'pdf');
                                $ext = strtoupper($pathInfo['extension'] ?? 'PDF');
                            }

                            $iconClass = match ($dataType) {
                                'pdf' => 'pdf',
                                'doc', 'docx' => 'doc',
                                'ppt', 'pptx' => 'pptx',
                                'mp4', 'avi', 'mov' => 'video',
                                'xls', 'xlsx' => 'xlsx',
                                'zip', 'rar', '7z' => 'zip',
                                'physical' => 'physical',
                                default => 'doc'
                            };

                            $statusClass = match ($statusValue) {
                                'available' => 'disponivel',
                                'reserved' => 'emprestado',
                                'active' => 'em-uso',
                                default => 'disponivel'
                            };

                            $status = match ($statusValue) {
                                'available' => 'Disponível',
                                'reserved' => 'Emprestado',
                                'active' => 'Em Uso',
                                default => 'Disponível'
                            };

                            $metaText1 = $owner ? 'Dono: ' . data_get($owner, 'username', 'Equipe') : 'Sistema';
                            $metaText2 = data_get($resource, 'metaText2', 'Sem informação');

                            if (data_get($resource, 'quantity_available')) {
                                $metaText2 = data_get($resource, 'quantity_available') . ' disponível(is)';
                            }

                            if ($digitalResource) {
                                $metaText2 = 'Arquivo digital';
                            }

                            if ($physicalResource) {
                                $metaText2 = 'Local: ' . (data_get($physicalResource, 'location', 'Não informado'));
                            }

                            $dataOwnerValue = data_get($resource, 'dataOwner');
                            if (!$dataOwnerValue) {
                                $dataOwnerValue = auth()->id() === $resourceOwnerId ? 'my' : 'other';
                            }
                        @endphp

                        <x-resource-card id="{{ data_get($resource, 'id') }}" iconClass="{{ $iconClass }}" ext="{{ $ext }}"
                            title="{{ data_get($resource, 'title') }}" statusClass="{{ $statusClass }}" status="{{ $status }}"
                            metaText1="{{ $metaText1 }}" metaText2="{{ $metaText2 }}"
                            desc="{{ data_get($resource, 'description', 'Sem descrição disponível.') }}"
                            dataType="{{ $dataType }}"
                            dataOwner="{{ $dataOwnerValue }}" />

                    @empty
                        <div class="no-resources">
                            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3>Nenhum recurso disponível</h3>
                            <p>Não há recursos disponíveis para empréstimo no momento.</p>
                        </div>
                    @endforelse
                @endif
            </div>
            <!-- /resources-grid -->
        </main>
    </div>

    <!-- ─── MODAL ─── -->
    <x-modal />

    <!-- ─── TOAST ─── -->
    <x-toast />

</body>

</html>