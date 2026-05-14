@php
    $baseLinkClass = 'block rounded-lg px-3 py-2 text-[#2c1c13] transition hover:bg-[#fff4ec] hover:text-[#FE6807] dark:text-white dark:hover:bg-[#171717] dark:hover:text-[#FE6807] md:border-0 md:p-0 md:hover:bg-transparent md:dark:hover:bg-transparent';
    $activeLinkClass = 'block rounded-lg px-3 py-2 text-[#FE6807] transition hover:bg-[#fff4ec] dark:hover:bg-[#171717] md:bg-transparent md:p-0 md:hover:bg-transparent md:dark:hover:bg-transparent';
    $user = auth()->user();
    $initials = $user
        ? collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('')
        : '';
@endphp

<div class="item">
    <nav class="fixed start-0 top-0 z-20 w-full border-b border-[#eee1d6] bg-white dark:border-[#241915] dark:bg-black">
        <div
            class="mx-auto grid max-w-screen-2xl grid-cols-[auto_1fr_auto] items-center gap-3 px-4 py-3 sm:px-5 md:py-4">
            <a href="{{ route('index') }}" class="flex min-w-0 items-center space-x-3 rtl:space-x-reverse">
                <picture>
                    <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
                    <img src="{{ asset('img/png/logo_wb.png') }}" alt="Logo VuTivi" class="h-8 w-auto sm:h-10" />
                </picture>
            </a>

            <form action="{{ route('library') }}" method="GET" class="hidden justify-self-center md:block md:w-full md:max-w-xl">
                <label for="navbar-search" class="sr-only">Pesquisar recursos</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#9f6a47]">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 15.5A6.5 6.5 0 1 0 9 2.5a6.5 6.5 0 0 0 0 13ZM14 14l3.5 3.5"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </span>
                    <input id="navbar-search" type="search" name="q" value="{{ request('q') }}"
                        placeholder="Pesquisar recursos"
                        class="min-h-11 w-full rounded-full border border-[#f3e4d8] bg-[#fffaf6] pl-11 pr-4 text-sm font-medium text-[#2c1c13] outline-none transition placeholder:text-[#a58570] focus:border-[#FE6807] focus:bg-white dark:border-[#241915] dark:bg-[#050505] dark:text-white dark:placeholder:text-[#83746b]" />
                </div>
            </form>

            <div class="flex items-center justify-end gap-2">
                @auth
                    <div
                        class="hidden items-center gap-3 rounded-full border border-[#f3e4d8] bg-[#fffaf6] py-1 pl-1 pr-3 dark:border-[#241915] dark:bg-[#050505] sm:flex">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-[#FE6807] text-xs font-bold uppercase text-white">
                            {{ $initials }}
                        </div>
                        <span class="max-w-36 truncate text-sm font-semibold text-[#2c1c13] dark:text-white">
                            {{ $user->name }}
                        </span>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="box-border rounded-base border border-transparent bg-[#FE6807] px-3 py-2 text-sm font-medium leading-5 text-white shadow-xs hover:bg-[#e15f07] focus:outline-none focus:ring-4 focus:ring-[#FE6807]/50 sm:px-4">
                        Entrar
                    </a>
                @endauth

                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-base p-2 text-sm text-[#2c1c13] hover:bg-[#fff4ec] hover:text-[#FE6807] focus:outline-none focus:ring-2 focus:ring-[#FE6807]/30 dark:text-white dark:hover:bg-[#171717] dark:hover:text-[#FE6807] lg:hidden"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Abrir menu principal</span>
                    <svg class="h-7 w-7" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M5 7h14M5 12h14M5 17h14" />
                    </svg>
                </button>
            </div>

            <div class="col-span-3 hidden w-full items-center justify-between lg:col-span-1 lg:col-start-2 lg:hidden"
                id="navbar-sticky">
                <div
                    class="mt-3 w-full rounded-2xl border border-[#eee1d6] bg-white p-3 font-medium shadow-[0_18px_34px_rgba(88,44,14,0.08)] dark:border-[#241915] dark:bg-black">
                    <form action="{{ route('library') }}" method="GET" class="mb-3 md:hidden">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar recursos"
                            class="min-h-11 w-full rounded-full border border-[#f3e4d8] bg-[#fffaf6] px-4 text-sm font-medium text-[#2c1c13] outline-none focus:border-[#FE6807] dark:border-[#241915] dark:bg-[#050505] dark:text-white" />
                    </form>

                    <ul class="flex flex-col gap-1">
                        @auth
                            <li><a href="{{ route('library') }}"
                                    class="{{ request()->routeIs('library') ? $activeLinkClass : $baseLinkClass }}">Biblioteca</a>
                            </li>
                            <li><a href="{{ route('mine') }}"
                                    class="{{ request()->routeIs('mine') ? $activeLinkClass : $baseLinkClass }}">Meus recursos</a>
                            </li>
                            <li><a href="{{ route('favorites') }}"
                                    class="{{ request()->routeIs('favorites') ? $activeLinkClass : $baseLinkClass }}">Recursos favoritos</a>
                            </li>
                            <li><a href="{{ route('borrowed') }}"
                                    class="{{ request()->routeIs('borrowed') ? $activeLinkClass : $baseLinkClass }}">Recursos de emprestimo</a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full rounded-lg px-3 py-2 text-left text-[#9f3b1f] transition hover:bg-[#fff4ec] dark:text-[#ffb07a] dark:hover:bg-[#171717]">
                                        Terminar sessao
                                    </button>
                                </form>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('index') }}"
                                    class="{{ request()->routeIs('index') ? $activeLinkClass : $baseLinkClass }}">Pagina Inicial</a>
                            </li>
                            <li>
                                <a href="{{ route('library') }}"
                                    class="{{ request()->routeIs('library') ? $activeLinkClass : $baseLinkClass }}">Recursos</a>
                            </li>
                            <li><a href="#" class="{{ $baseLinkClass }}">Sobre</a></li>
                            <li><a href="#" class="{{ $baseLinkClass }}">Contacto</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <x-sidebar />
</div>
