<!doctype html>
<html lang="pt">

<x-head title="Biblioteca de Recursos" />

<body class="home-layout">
    <x-navbar />

    <main class="item banner-shell">
        <section class="banner" data-banner-parallax style="background-image: url('/img/png/book_background.png')">
            <div class="banner__content" data-parallax-depth="18">
                <p class="banner__eyebrow">Biblioteca digital para equipas e leitores</p>
                <h1 class="banner__title">
                    <span class="banner__title-accent">Vutivi Library</span>
                    <strong>Organiza</strong> os seus recursos num espaço mais claro e rapido de explorar.
                </h1>
                <p class="banner__copy">
                    Explore recursos digitais e físicos num so lugar, com uma vitrine visual mais forte logo na entrada.
                </p>
                <div class="banner__meta">
                    <span class="banner__meta-pill">Acesso rapido</span>
                    <span class="banner__meta-pill">Recursos digitais</span>
                    <span class="banner__meta-pill">Partilha simples</span>
                </div>
                <div class="banner__actions">
                    <a href="{{ route('library') }}" class="banner__button">Ir para a biblioteca</a>
                </div>
            </div>
            <div class="banner__floating-rail" aria-hidden="true">
                <aside class="banner__floating banner__floating--top" data-parallax-depth="-14">
                    <span class="banner__floating-label">Curadoria</span>
                    <strong>Recursos bem organizados</strong>
                    <p>Uma vitrine clara para encontrar documentos, ebooks e guias com menos esforço.</p>
                </aside>
                <aside class="banner__floating banner__floating--formats" data-parallax-depth="-18">
                    <span class="banner__floating-label">Formatos</span>
                    <strong>Conteúdo para vários usos</strong>
                    <div class="banner__floating-list">
                        <span>PDF</span>
                        <span>Audio</span>
                        <span>Video</span>
                        <span>Ebooks</span>
                        <span>Guias</span>
                        <span>Slides</span>
                    </div>
                </aside>
                <aside class="banner__floating banner__floating--bottom" data-parallax-depth="-24">
                    <span class="banner__floating-dot"></span>
                    <div>
                        <strong>Atualizações em destaque</strong>
                        <p>Novos materiais e recomendações com acesso mais fluído.</p>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>

</html>
