@auth
    @php
        $iconClass = 'h-4 w-4 flex-none';
        $linkBase = 'flex min-h-10 items-center gap-3 rounded-xl px-3 text-sm font-semibold transition duration-200';
        $linkIdle = 'text-[#5e4334] hover:bg-[#fff7f0] hover:text-[#FE6807] dark:text-[#d5c7be] dark:hover:bg-[#17120f]';
        $linkActive = 'bg-[#fff1e6] text-[#FE6807] ring-1 ring-[#ffd8bf] shadow-[0_10px_22px_rgba(254,104,7,0.08)] dark:bg-[#17120f] dark:ring-[#4a2a1b]';
        $groups = [
            [
                'label' => 'Biblioteca',
                'icon' => 'book-open',
                'open' => request()->routeIs('library', 'favorites', 'categories'),
                'links' => [
                    ['label' => 'Explorar recursos', 'route' => 'library', 'active' => ['library'], 'icon' => 'search'],
                    ['label' => 'Favoritos', 'route' => 'favorites', 'active' => ['favorites'], 'icon' => 'heart'],
                    ['label' => 'Categorias', 'route' => 'categories', 'active' => ['categories'], 'icon' => 'grid'],
                ],
            ],
            [
                'label' => 'Meus Recursos',
                'icon' => 'archive',
                'open' => request()->routeIs('mine', 'resources.create', 'physical-resources.*', 'digital-resources.*'),
                'links' => [
                    ['label' => 'Meus recursos', 'route' => 'mine', 'active' => ['mine'], 'icon' => 'layers'],
                    ['label' => 'Adicionar recurso', 'route' => 'resources.create', 'active' => ['resources.create', 'physical-resources.create', 'digital-resources.create'], 'icon' => 'plus-circle'],
                ],
            ],
            [
                'label' => 'Empréstimos',
                'icon' => 'repeat',
                'open' => request()->routeIs('borrowed', 'lent', 'loan-alerts', 'loan-history', 'reservations.*'),
                'links' => [
                    ['label' => 'Empréstimos ativos', 'route' => 'borrowed', 'active' => ['borrowed', 'reservations.show'], 'icon' => 'download'],
                    ['label' => 'Solicitações', 'route' => 'lent', 'active' => ['lent'], 'icon' => 'upload'],
                    ['label' => 'Histórico de empréstimos', 'route' => 'loan-history', 'active' => ['loan-history'], 'icon' => 'history'],
                    ['label' => 'Notificações', 'route' => 'loan-alerts', 'active' => ['loan-alerts'], 'icon' => 'clock'],
                ],
            ],
            [
                'label' => 'Conta',
                'icon' => 'user',
                'open' => request()->routeIs('account', 'users.*'),
                'links' => [
                    ['label' => 'Perfil', 'route' => 'account', 'active' => ['account'], 'icon' => 'user'],
                    ['label' => 'Configurações', 'route' => 'users.edit', 'params' => [auth()->id()], 'active' => ['users.edit'], 'icon' => 'settings'],
                ],
            ],
        ];

        $svg = function (string $name, string $class = 'h-4 w-4') {
            $paths = [
                'book-open' => '<path d="M2 4.5A3.5 3.5 0 0 1 5.5 3H11v18H5.5A3.5 3.5 0 0 0 2 22.5v-18Z"/><path d="M22 4.5A3.5 3.5 0 0 0 18.5 3H13v18h5.5a3.5 3.5 0 0 1 3.5 1.5v-18Z"/>',
                'archive' => '<path d="M21 8v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8"/><path d="M3 3h18v5H3z"/><path d="M10 12h4"/>',
                'repeat' => '<path d="m17 1 4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="m7 23-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>',
                'user' => '<path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/>',
                'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
                'heart' => '<path d="m12 21-1.45-1.32C5.4 15.02 2 11.93 2 8.15 2 5.06 4.42 3 7.5 3c1.74 0 3.41.8 4.5 2.05A6.02 6.02 0 0 1 16.5 3C19.58 3 22 5.06 22 8.15c0 3.78-3.4 6.87-8.55 11.53L12 21Z"/>',
                'grid' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
                'layers' => '<path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/>',
                'plus-circle' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>',
                'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>',
                'upload' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/>',
                'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
                'history' => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/><path d="M12 7v5l3 2"/>',
                'settings' => '<path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z"/><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .6 1.8 1.8 0 0 0-.5 1.27V21a2 2 0 1 1-4 0v-.09A1.8 1.8 0 0 0 8 19.4a1.8 1.8 0 0 0-1.98.36l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-.6-1 1.8 1.8 0 0 0-1.27-.5H2.6a2 2 0 1 1 0-4h.09A1.8 1.8 0 0 0 4.6 8a1.8 1.8 0 0 0-.36-1.98l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.8 1.8 0 0 0 9 4.6a1.8 1.8 0 0 0 1-.6 1.8 1.8 0 0 0 .5-1.27V2.6a2 2 0 1 1 4 0v.09A1.8 1.8 0 0 0 16 4.6a1.8 1.8 0 0 0 1.98-.36l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.8 1.8 0 0 0 19.4 9c.4.22.73.55.95.95.2.35.3.75.3 1.15s-.1.8-.3 1.15c-.22.4-.55.73-.95.95Z"/>',
            ];

            return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($paths[$name] ?? $paths['book-open']) . '</svg>';
        };
    @endphp

    <aside class="app-sidebar fixed left-0 top-[73px] z-20 hidden h-[calc(100vh-73px)] w-64 border-r border-[#eee1d6] bg-white/92 px-3 py-5 shadow-[18px_0_38px_rgba(88,44,14,0.06)] backdrop-blur-xl dark:border-[#241915] dark:bg-[#090807]/94 lg:flex lg:flex-col">
        <nav class="flex flex-1 flex-col gap-2">
            @foreach ($groups as $group)
                <details class="group/sidebar overflow-hidden rounded-2xl" {{ $group['open'] ? 'open' : '' }}>
                    <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between rounded-xl px-3 text-sm font-black text-[#2c1c13] transition hover:bg-[#fff7f0] dark:text-white dark:hover:bg-[#17120f] [&::-webkit-details-marker]:hidden">
                        <span class="flex items-center gap-3">
                            {!! $svg($group['icon'], $iconClass) !!}
                            {{ $group['label'] }}
                        </span>
                        <svg class="h-4 w-4 transition duration-300 group-open/sidebar:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>

                    <div class="sidebar-panel grid gap-1 pl-2 pt-1">
                        @foreach ($group['links'] as $link)
                            <a href="{{ route($link['route'], $link['params'] ?? []) }}" class="{{ $linkBase }} {{ request()->routeIs(...$link['active']) ? $linkActive : $linkIdle }}">
                                {!! $svg($link['icon'], 'h-4 w-4 flex-none opacity-80') !!}
                                <span class="truncate">{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="border-t border-[#f3e4d8] pt-4 dark:border-[#241915]">
            @csrf
            <button type="submit" class="flex min-h-10 w-full items-center gap-3 rounded-xl px-3 text-left text-sm font-bold text-[#9f3b1f] transition hover:bg-[#fff1e6] dark:text-[#ffb07a] dark:hover:bg-[#17120f]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                Terminar sessão
            </button>
        </form>
    </aside>
@endauth
