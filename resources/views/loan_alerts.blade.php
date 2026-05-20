<!doctype html>
<html lang="pt">
<x-head title="Prazos próximos" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <section class="mx-auto max-w-5xl rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Notificações</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">Empréstimos próximos do prazo</h1>
            <div class="mt-5 grid gap-3">
                @forelse ($reservations as $reservation)
                    <article class="rounded-lg border border-[#eadfce] bg-[#fffaf5] p-4 dark:border-[#332820] dark:bg-[#0f0d0b]">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="font-semibold text-[#241b14] dark:text-white">{{ $reservation->resource?->title }}</h2>
                                <p class="text-sm text-[#66594d] dark:text-[#cfc5ba]">Com {{ $reservation->user?->name }} até {{ optional($reservation->end_date)->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('reservations.show', $reservation->id) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-[#FE6807] px-4 text-sm font-semibold text-white">Ver empréstimo</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-[#eadfce] bg-[#fffaf5] p-6 text-center text-sm font-semibold text-[#66594d] dark:border-[#332820] dark:bg-[#0f0d0b] dark:text-[#cfc5ba]">
                        Nenhum empréstimo está perto de terminar nos próximos 3 dias.
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $reservations->links() }}</div>
        </section>
    </main>
</body>
</html>
