@props(['logo' => 'Vutivi'])

<aside class="sidebar">
    <div class="sidebar-logo">{{ $logo ?? 'Vutivi' }}</div>

    <div>
        <p class="sidebar-section-title">Menu</p>
        <ul class="nav-menu">
            <li class="nav-item {{ request()->routeIs('library') ? 'active' : '' }}">
                <a href="{{ route('library') }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                    </svg>
                    Biblioteca
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('mine') ? 'active' : '' }}">
                <a href="{{ route('mine') }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16v12H4z" />
                        <path d="M8 6v12" />
                    </svg>
                    Meus recursos
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('favorites') ? 'active' : '' }}">
                <a href="{{ route('favorites') }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                    </svg>
                    Favoritos
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('categories') ? 'active' : '' }}">
                <a href="{{ route('categories') }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                    Categorias
                </a>
            </li>
            <li class="nav-item">
                <a href="#">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                    Mensagens
                </a>
            </li>
        </ul>
    </div>

    <hr class="sidebar-divider" />

    <div>
        <p class="sidebar-section-title">Tipos</p>
        <ul class="type-list">
            <li><a href="#" data-filter="pdf">Documentos</a></li>
            <li><a href="#" data-filter="video">Vídeos</a></li>
            <li><a href="#" data-filter="xlsx">Planilhas</a></li>
            <li><a href="#" data-filter="zip">Arquivos</a></li>
        </ul>
    </div>
</aside>