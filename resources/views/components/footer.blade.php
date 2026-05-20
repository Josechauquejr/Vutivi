<footer class="border-t border-[#eadfce] bg-white px-4 py-12 dark:border-[#241915] dark:bg-[#080706] sm:px-6 lg:px-10 xl:px-16">
    <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-[1.2fr_repeat(4,1fr)]">
        <div>
            <picture>
                <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
                <img src="{{ asset('img/png/logo_wb.png') }}" alt="Logo VuTivi" class="h-11 w-auto" />
            </picture>
            <p class="mt-4 max-w-sm text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">VUTIVI vem do xitsonga e significa conhecimento. É uma biblioteca digital e física para gestão, descoberta e partilha de aprendizagem.</p>
        </div>

        @foreach ([
            'Links rapidos' => [['Inicio', route('index')], ['Biblioteca', route('library')], ['Sobre', route('about')], ['Login', route('login')]],
            'Categorias' => [['Digitais', route('library', ['type' => 'digital'])], ['Fisicos', route('library', ['type' => 'physical'])], ['Populares', route('library', ['sort' => 'popular'])]],
            'Suporte' => [['Ajuda', '#'], ['Termos', '#'], ['Contacto', '#']],
        ] as $heading => $links)
            <div>
                <h2 class="text-sm font-black uppercase tracking-[0.18em] text-[#241b14] dark:text-white">{{ $heading }}</h2>
                <ul class="mt-4 space-y-2">
                    @foreach ($links as [$label, $href])
                        <li><a href="{{ $href }}" class="text-sm font-semibold text-[#66594d] hover:text-[#FE6807] dark:text-[#cfc5ba]">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <div>
            <h2 class="text-sm font-black uppercase tracking-[0.18em] text-[#241b14] dark:text-white">Contactos</h2>
            <p class="mt-4 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">biblioteca@vutivi.local<br>+258 84 000 0000</p>
            <div class="mt-4 flex gap-2">
                @foreach (['f', 'in', 'x'] as $social)
                    <a href="#" class="flex h-9 w-9 items-center justify-center rounded-full border border-[#eadfce] text-xs font-black text-[#66594d] hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#332820] dark:text-[#cfc5ba]">{{ $social }}</a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="mx-auto mt-10 flex max-w-7xl flex-col gap-3 border-t border-[#f3e4d8] pt-6 text-xs font-semibold text-[#806856] dark:border-[#241915] dark:text-[#bcae9f] sm:flex-row sm:items-center sm:justify-between">
        <p>&copy; {{ date('Y') }} Vutivi Library. Todos os direitos reservados.</p>
        <p>Conhecimento, leitura e tecnologia moderna.</p>
    </div>
</footer>
