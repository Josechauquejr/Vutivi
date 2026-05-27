<!doctype html>
<html lang="pt">
<x-head title="Prazos próximos" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <section class="mx-auto max-w-5xl rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Notificações</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">Notificações de empréstimos</h1>
            <div class="mt-5 grid gap-3">
                @forelse ($reservations as $reservation)
                    @php
                        $isOwnerExtension = (int) ($reservation->resource?->owner_id ?? 0) === (int) auth()->id() && $reservation->status === \App\Models\Reservation::STATUS_EXTENSION_PENDING;
                        $isBorrowerDecision = (int) $reservation->user_id === (int) auth()->id() && filled($reservation->extension_decision);
                        $isApproved = $isBorrowerDecision && $reservation->extension_decision === \App\Models\Reservation::EXTENSION_APPROVED;
                        $isDenied   = $isBorrowerDecision && $reservation->extension_decision === \App\Models\Reservation::EXTENSION_DENIED;

                        $daysRemaining = (! $isOwnerExtension && ! $isBorrowerDecision && $reservation->end_date)
                            ? (int) now()->startOfDay()->diffInDays($reservation->end_date, false)
                            : null;
                        $isOverdue = $daysRemaining !== null && $daysRemaining < 0;

                        $title = $isOwnerExtension
                            ? 'Pedido de extensão pendente'
                            : ($isBorrowerDecision
                                ? ($isApproved ? 'Extensão aprovada' : 'Extensão recusada')
                                : ($isOverdue ? 'Prazo ultrapassado' : $reservation->resource?->title));

                        $description = $isOwnerExtension
                            ? (($reservation->user?->name ?? 'Utilizador') . ' pediu mais tempo para ' . ($reservation->resource?->title ?? 'este recurso') . '.')
                            : ($isBorrowerDecision
                                ? (($reservation->resource?->title ?? 'O recurso') . ($isApproved ? ' recebeu novo prazo até ' . optional($reservation->end_date)->format('d/m/Y') : ' permanece com o prazo original.'))
                                : (($reservation->resource?->title ?? 'Recurso') . ' com ' . ($reservation->user?->name ?? 'utilizador') . ' — devolução em ' . optional($reservation->end_date)->format('d/m/Y')));

                        $cardClass = match(true) {
                            $isApproved                              => 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/20',
                            $isDenied || $isOverdue                  => 'border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-950/20',
                            $daysRemaining !== null && ! $isOverdue  => 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/20',
                            default                                  => 'border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-950/20',
                        };
                        $titleClass = match(true) {
                            $isApproved                              => 'text-emerald-900 dark:text-emerald-100',
                            $isDenied || $isOverdue                  => 'text-red-900 dark:text-red-100',
                            $daysRemaining !== null && ! $isOverdue  => 'text-emerald-900 dark:text-emerald-100',
                            default                                  => 'text-amber-900 dark:text-amber-100',
                        };
                        $descClass = match(true) {
                            $isApproved                              => 'text-emerald-700 dark:text-emerald-300',
                            $isDenied || $isOverdue                  => 'text-red-700 dark:text-red-300',
                            $daysRemaining !== null && ! $isOverdue  => 'text-emerald-700 dark:text-emerald-300',
                            default                                  => 'text-amber-700 dark:text-amber-300',
                        };

                        $daysLabel = null;
                        $daysLabelClass = '';
                        if ($daysRemaining !== null) {
                            if ($isOverdue) {
                                $d = abs($daysRemaining);
                                $daysLabel = '-' . $d . ' ' . ($d === 1 ? 'dia' : 'dias');
                                $daysLabelClass = 'mt-1 inline-flex rounded-full border border-red-200 bg-red-100 px-2.5 py-0.5 text-xs font-black text-red-700 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300';
                            } else {
                                $daysLabel = '+' . $daysRemaining . ' ' . ($daysRemaining === 1 ? 'dia' : 'dias');
                                $daysLabelClass = 'mt-1 inline-flex rounded-full border border-emerald-200 bg-emerald-100 px-2.5 py-0.5 text-xs font-black text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300';
                            }
                        }
                    @endphp
                    <article class="rounded-lg border p-4 {{ $cardClass }}">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="font-semibold {{ $titleClass }}">{{ $title }}</h2>
                                <p class="text-sm {{ $descClass }}">{{ $description }}</p>
                                @if ($daysLabel)
                                    <span class="{{ $daysLabelClass }}">{{ $daysLabel }}</span>
                                @endif
                            </div>
                            <a href="{{ route('reservations.show', $reservation->id) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-[#FE6807] px-4 text-sm font-semibold text-white">Ver empréstimo</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-[#eadfce] bg-[#fffaf5] p-6 text-center text-sm font-semibold text-[#66594d] dark:border-[#332820] dark:bg-[#0f0d0b] dark:text-[#cfc5ba]">
                        Nenhuma notificação de empréstimo pendente.
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $reservations->links() }}</div>
        </section>
    </main>
</body>
</html>
