@php
    $baseLinkClass = 'block rounded-lg px-3 py-2 text-[#2c1c13] transition hover:bg-[#fff4ec] hover:text-[#FE6807] dark:text-white dark:hover:bg-[#171717] dark:hover:text-[#FE6807] md:border-0 md:p-0 md:hover:bg-transparent md:dark:hover:bg-transparent';
    $activeLinkClass = 'block rounded-lg px-3 py-2 text-[#FE6807] transition hover:bg-[#fff4ec] dark:hover:bg-[#171717] md:bg-transparent md:p-0 md:hover:bg-transparent md:dark:hover:bg-transparent';
@endphp

<div class="item">
    <nav class="fixed start-0 top-0 z-20 w-full border-b border-[#eee1d6] bg-white dark:border-[#241915] dark:bg-black">
        <div class="mx-auto flex max-w-screen-xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-5 md:gap-0 md:py-4">
            <a href="{{ route('index') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <picture>
                    <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
                    <img src="{{ asset('img/png/logo_wb.png') }}" alt="Logo VuTivi" class="h-8 w-auto sm:h-10" />
                </picture>
                <span class="self-center text-xl text-heading font-semibold whitespace-nowrap"></span>
            </a>
            <div class="flex items-center gap-2 md:order-2 md:gap-0 rtl:space-x-reverse">
                <a href="{{ route('login') }}"
                    class="box-border rounded-base border border-transparent bg-[#FE6807] px-3 py-2 text-sm font-medium leading-5 text-white shadow-xs hover:bg-[#e15f07] focus:outline-none focus:ring-4 focus:ring-[#FE6807]/50 sm:px-4">
                    Entrar
                </a>
                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-base p-2 text-sm text-[#2c1c13] hover:bg-[#fff4ec] hover:text-[#FE6807] focus:outline-none focus:ring-2 focus:ring-[#FE6807]/30 dark:text-white dark:hover:bg-[#171717] dark:hover:text-[#FE6807] md:hidden"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Abrir menu principal</span>
                    <svg class="w-7 h-7" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M5 7h14M5 12h14M5 17h14" />
                    </svg>
                </button>
            </div>
            <div class="hidden w-full items-center justify-between md:order-1 md:flex md:w-auto" id="navbar-sticky">
                <ul
                    class="mt-3 flex max-h-[calc(100vh-84px)] flex-col overflow-y-auto rounded-2xl border border-[#eee1d6] bg-white p-3 font-medium shadow-[0_18px_34px_rgba(88,44,14,0.08)] dark:border-[#241915] dark:bg-black md:mt-0 md:max-h-none md:flex-row md:space-x-8 md:overflow-visible md:border-0 md:bg-white md:p-0 md:shadow-none md:dark:bg-black rtl:space-x-reverse">
                    <li>
                        <a href="{{ route('index') }}"
                            class="{{ request()->routeIs('index') ? $activeLinkClass : $baseLinkClass }}"
                            @if (request()->routeIs('index')) aria-current="page" @endif>Pagina Inicial</a>
                    </li>
                    <li>
                        <a href="{{ route('library') }}"
                            class="{{ request()->routeIs('library') ? $activeLinkClass : $baseLinkClass }}"
                            @if (request()->routeIs('library')) aria-current="page" @endif>Recursos</a>
                    </li>
                    <li>
                        <a href="#"
                            class="{{ $baseLinkClass }}">Sobre</a>
                    </li>
                    <li>
                        <a href="#"
                            class="{{ $baseLinkClass }}">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
