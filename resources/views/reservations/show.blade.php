<!doctype html>
<html lang="pt">
<x-head title="Detalhe do emprestimo" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        @php
            $resource = $reservation->resource;
            $daysRemaining = $reservation->returned_at ? 0 : now()->startOfDay()->diffInDays($reservation->end_date, false);
            $status = $reservation->returned_at ? 'devolvido' : ($daysRemaining < 0 ? 'atrasado' : ($reservation->status ?? 'ativo'));
            $statusClass = match ($status) {
                'pending', 'extension_pending' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/30 dark:text-amber-100 dark:border-amber-900/50',
                'approved', 'in_use', 'extended' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-100 dark:border-emerald-900/50',
                'atrasado' => 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/30 dark:text-red-100 dark:border-red-900/50',
                default => 'bg-slate-50 text-slate-800 border-slate-200 dark:bg-slate-900/30 dark:text-slate-100 dark:border-slate-800',
            };
        @endphp
        <x-breadcrumbs :items="[['label' => 'Empréstimos', 'url' => route('borrowed')], ['label' => $resource?->title ?? 'Detalhe']]" />
        <section class="mx-auto max-w-5xl rounded-2xl border border-[#eadfce] bg-white p-5 shadow-[0_18px_50px_rgba(54,39,25,0.08)] dark:border-[#27211a] dark:bg-[#090909] md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Gestão de empréstimo</p>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <h1 class="text-3xl font-semibold text-[#241b14] dark:text-white">{{ $resource?->title }}</h1>
                <span class="{{ $statusClass }} rounded-full border px-3 py-1 text-xs font-black uppercase tracking-[0.14em]">{{ str_replace('_', ' ', $status) }}</span>
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-[#fffaf5] p-4 dark:bg-[#0f0d0b]"><span class="text-xs font-bold uppercase tracking-[0.16em] text-[#9b6b3f]">Utilizador</span><strong class="mt-2 block">{{ $reservation->user?->name }}</strong></div>
                <div class="rounded-xl bg-[#fffaf5] p-4 dark:bg-[#0f0d0b]"><span class="text-xs font-bold uppercase tracking-[0.16em] text-[#9b6b3f]">Data de entrega</span><strong class="mt-2 block">{{ optional($reservation->start_date)->format('d/m/Y') }}</strong></div>
                <div class="rounded-xl bg-[#fffaf5] p-4 dark:bg-[#0f0d0b]"><span class="text-xs font-bold uppercase tracking-[0.16em] text-[#9b6b3f]">Data de devolução</span><strong class="mt-2 block">{{ optional($reservation->end_date)->format('d/m/Y') }}</strong></div>
                <div class="rounded-xl bg-[#fffaf5] p-4 dark:bg-[#0f0d0b]"><span class="text-xs font-bold uppercase tracking-[0.16em] text-[#9b6b3f]">Dias restantes</span><strong class="mt-2 block">{{ $reservation->returned_at ? 'Devolvido' : ($daysRemaining >= 0 ? $daysRemaining . ' dias' : abs($daysRemaining) . ' dias em atraso') }}</strong></div>
            </div>

            <div class="mt-6 rounded-2xl border border-[#eadfce] bg-[#fffaf5] p-5 dark:border-[#332820] dark:bg-[#0f0d0b]">
                <h2 class="text-xl font-semibold text-[#241b14] dark:text-white">Histórico</h2>
                <ol class="mt-4 grid gap-3">
                    @foreach ([['Solicitado', $reservation->created_at], ['Aprovado', $reservation->approved_at], ['Devolvido', $reservation->returned_at]] as [$label, $date])
                        <li class="flex items-center gap-3 text-sm text-[#66594d] dark:text-[#cfc5ba]">
                            <span class="h-3 w-3 rounded-full {{ $date ? 'bg-[#FE6807]' : 'bg-[#d8c5ad]' }}"></span>
                            <span><strong>{{ $label }}</strong> {{ $date ? optional($date)->format('d/m/Y H:i') : 'pendente' }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                @if ($resource)
                    <a href="{{ $resource->slug ? route('resources.public.show', $resource->slug) : route('resources.show', $resource->id) }}" class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] dark:border-[#332820] dark:text-[#d8cec3]">Ver recurso</a>
                @endif
                <a href="{{ route('reservations.edit', $reservation->id) }}" class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] dark:border-[#332820] dark:text-[#d8cec3]">Editar</a>
                @if (! $reservation->returned_at)
                    @if ((int) $reservation->user_id === (int) auth()->id() && $reservation->status !== \App\Models\Reservation::STATUS_EXTENSION_PENDING)
                        <form method="POST" action="{{ route('reservations.extension.request', $reservation->id) }}" class="flex flex-col gap-2 sm:flex-row">
                            @csrf
                            @method('PATCH')
                            <input name="extension_reason" placeholder="Motivo da extensão" class="min-h-10 rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                            <button class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] dark:border-[#332820] dark:text-[#d8cec3]">Pedir extensão</button>
                        </form>
                    @endif
                    @if ($resource && (int) $resource->owner_id === (int) auth()->id() && $reservation->status === \App\Models\Reservation::STATUS_EXTENSION_PENDING)
                        <form method="POST" action="{{ route('reservations.extension.approve', $reservation->id) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Aceitar extensão</button>
                        </form>
                        <form method="POST" action="{{ route('reservations.extension.deny', $reservation->id) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white">Negar extensão</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('reservations.return', $reservation->id) }}">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-lg bg-[#FE6807] px-4 py-2 text-sm font-semibold text-white">Marcar devolvido</button>
                    </form>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
