<!doctype html>
<html lang="pt">
<x-head title="Termos de empréstimo" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Biblioteca', 'url' => route('library')], ['label' => $resource->title, 'url' => $resource->slug ? route('resources.public.show', $resource->slug) : route('resources.show', $resource->id)], ['label' => 'Termos e condições']]" />
        <section class="mx-auto max-w-3xl rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Termos e condições</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">{{ $resource->title }}</h1>
            <div class="mt-5 max-h-80 overflow-y-auto rounded-lg border border-[#decbb8] bg-[#fffaf5] p-4 text-sm leading-6 text-[#5f4632] dark:border-[#332820] dark:bg-[#0f0d0b] dark:text-[#d8cec3]">
                @if ($terms)
                    <h2 class="mb-2 text-lg font-semibold text-[#241b14] dark:text-white">{{ $terms->title }}</h2>
                    {!! nl2br(e($terms->content)) !!}
                @else
                    Termos gerais de empréstimo da VUTIVI

                    Ao solicitar este recurso físico, o utilizador compromete-se a utilizá-lo exclusivamente para fins académicos, formativos ou de investigação, preservando o seu estado físico e respeitando as regras de circulação definidas pela biblioteca.

                    O recurso deve ser devolvido até à data indicada no sistema. Em caso de atraso, dano, perda ou impossibilidade de devolução, o utilizador deve comunicar imediatamente o responsável pelo recurso para que sejam acordadas medidas adequadas.

                    A plataforma regista dados necessários para gestão do empréstimo, incluindo utilizador, recurso, datas, estado e histórico de movimentação. Esses dados são utilizados apenas para organização da biblioteca, segurança operacional e proteção dos recursos partilhados.

                    Pedidos de extensão podem ser avaliados pelo dono do recurso conforme disponibilidade, histórico de uso e necessidade académica. A aprovação não é automática e pode ser recusada quando houver reservas, atrasos ou risco de indisponibilidade para outros utilizadores.
                @endif
            </div>
            <form method="POST" action="{{ route('reservations.terms.store') }}" class="mt-5 grid gap-4">
                @csrf
                <input type="hidden" name="resource_id" value="{{ $resource->id }}">
                <label class="flex items-start gap-3 rounded-lg border border-[#eadfce] bg-[#fffaf5] p-4 text-sm font-semibold text-[#5f4632] dark:border-[#332820] dark:bg-[#0f0d0b] dark:text-[#d8cec3]">
                    <input type="checkbox" name="terms_accepted" value="1" required class="mt-1">
                    Aceito os termos e condições de empréstimo deste recurso físico.
                </label>
                <button class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#FE6807] px-5 text-sm font-semibold text-white">Aceitar e solicitar empréstimo</button>
            </form>
        </section>
    </main>
</body>
</html>
