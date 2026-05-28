<!doctype html>
<html lang="pt">
<x-head title="Listas de leitura" />

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Listas de leitura']]" />
        <section class="mx-auto max-w-4xl space-y-5">
            <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Biblioteca pessoal
                        </p>
                        <h1 class="mt-1 text-3xl font-semibold text-[#241b14] dark:text-white">Listas de leitura</h1>
                    </div>
                    <form method="POST" action="{{ route('reading-lists.store') }}" class="flex gap-2">
                        @csrf
                        <input name="name" type="text" placeholder="Nome da nova lista…" required maxlength="100"
                            class="min-w-0 flex-1 rounded-lg border border-[#ddd0be] bg-white px-3 py-2 text-sm text-[#241b14] placeholder-[#9b8b7c] dark:border-[#332820] dark:bg-[#111] dark:text-white" />
                        <button type="submit"
                            class="shrink-0 rounded-lg bg-[#FE6807] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e55c00]">Criar</button>
                    </form>
                </div>
            </div>

            @forelse ($lists as $list)
                <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-[#241b14] dark:text-white">{{ $list->name }}
                            <span
                                class="ml-1 text-sm font-normal text-[#66594d] dark:text-[#cfc5ba]">({{ $list->items_count }})</span>
                        </h2>
                        <form method="POST" action="{{ route('reading-lists.destroy', $list->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline"
                                onclick="return confirm('Eliminar lista?')">Eliminar</button>
                        </form>
                    </div>

                    @if ($list->items->isEmpty())
                        <p class="mt-3 text-sm text-[#66594d] dark:text-[#cfc5ba]">Nenhum recurso nesta lista.</p>
                    @else
                        <ul class="mt-3 space-y-2">
                            @foreach ($list->items as $item)
                                @php $resource = $item->resource; @endphp
                                <li
                                    class="flex items-center justify-between gap-3 rounded-lg border border-[#eadfce] bg-[#fffaf5] px-3 py-2 dark:border-[#332820] dark:bg-[#0f0d0b]">
                                    <div class="min-w-0">
                                        <a href="{{ $resource?->slug ? route('resources.public.show', $resource->slug) : '#' }}"
                                            class="truncate font-medium text-[#241b14] hover:underline dark:text-white">
                                            {{ $resource?->title ?? 'Recurso removido' }}
                                        </a>
                                        <p class="text-xs text-[#9b8b7c]">{{ ucfirst($resource?->type ?? '') }}</p>
                                    </div>
                                    <form method="POST"
                                        action="{{ route('reading-lists.items.remove', [$list->id, $item->resource_id]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="shrink-0 text-xs text-red-400 hover:underline">Remover</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @empty
                <div
                    class="rounded-2xl border border-[#eadfce] bg-white p-8 text-center dark:border-[#27211a] dark:bg-[#090909]">
                    <p class="text-[#66594d] dark:text-[#cfc5ba]">Ainda não tem listas. Crie uma acima ou adicione recursos
                        directamente.</p>
                </div>
            @endforelse
        </section>
    </main>
</body>

</html>