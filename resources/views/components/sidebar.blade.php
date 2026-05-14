@auth
    @php
        $sidebarLinks = [
            ['label' => 'Biblioteca', 'route' => 'library', 'active' => ['library']],
            ['label' => 'Meus recursos', 'route' => 'mine', 'active' => ['mine']],
            ['label' => 'Recursos favoritos', 'route' => 'favorites', 'active' => ['favorites']],
            ['label' => 'Recursos de emprestimo', 'route' => 'borrowed', 'active' => ['borrowed', 'reservations.*']],
        ];
    @endphp

    <aside
        class="app-sidebar fixed left-0 top-[73px] z-10 hidden h-[calc(100vh-73px)] w-64 border-r border-[#eee1d6] bg-white/95 px-4 py-5 shadow-[18px_0_38px_rgba(88,44,14,0.06)] backdrop-blur dark:border-[#241915] dark:bg-black/95 lg:flex lg:flex-col">
        <nav class="flex flex-1 flex-col gap-1">
            @foreach ($sidebarLinks as $link)
                <a href="{{ route($link['route']) }}"
                    class="{{ request()->routeIs(...$link['active']) ? 'bg-[#fff1e6] text-[#FE6807] ring-1 ring-[#ffd8bf] dark:bg-[#120d0a] dark:ring-[#4a2a1b]' : 'text-[#5e4334] hover:bg-[#fff7f0] hover:text-[#FE6807] dark:text-[#d5c7be] dark:hover:bg-[#171717]' }} flex min-h-11 items-center rounded-xl px-4 text-sm font-semibold transition">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="border-t border-[#f3e4d8] pt-4 dark:border-[#241915]">
            @csrf
            <button type="submit"
                class="flex min-h-11 w-full items-center rounded-xl px-4 text-left text-sm font-semibold text-[#9f3b1f] transition hover:bg-[#fff1e6] dark:text-[#ffb07a] dark:hover:bg-[#120d0a]">
                Terminar sessao
            </button>
        </form>
    </aside>
@endauth
