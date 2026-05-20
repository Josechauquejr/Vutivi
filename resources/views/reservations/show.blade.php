<!doctype html>
<html lang="pt">
<x-head title="Detalhes do empréstimo" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        @php
            $resource = $reservation->resource;
            $isOwner = $resource && (int) $resource->owner_id === (int) auth()->id();
            $isBorrower = (int) $reservation->user_id === (int) auth()->id();
            $daysRemaining = $reservation->returned_at ? 0 : now()->startOfDay()->diffInDays($reservation->end_date, false);
            $isOverdue = ! $reservation->returned_at && $daysRemaining < 0;
            $isDueSoon = ! $reservation->returned_at && ! $isOverdue && $daysRemaining <= 3;
            $visualStatus = $reservation->returned_at ? 'returned' : ($isOverdue ? 'overdue' : $reservation->status);
            $statusLabels = [
                'pending' => 'Pendente',
                'approved' => 'Aprovado',
                'in_use' => 'Em uso',
                'extension_pending' => 'Extensão solicitada',
                'extended' => 'Prazo estendido',
                'returned' => 'Devolvido',
                'cancelled' => 'Cancelado',
                'denied' => 'Recusado',
                'overdue' => 'Atrasado',
            ];
            $statusClass = match ($visualStatus) {
                'pending', 'extension_pending' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-100',
                'denied', 'cancelled', 'overdue' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-100',
                'returned' => 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-800 dark:bg-slate-900/30 dark:text-slate-100',
                default => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-100',
            };
            $icons = [
                'document-clock' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M12 13v3l2 1"/>',
                'check-circle' => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-5"/>',
                'book-open' => '<path d="M2 4.5A3.5 3.5 0 0 1 5.5 3H11v18H5.5A3.5 3.5 0 0 0 2 22.5v-18Z"/><path d="M22 4.5A3.5 3.5 0 0 0 18.5 3H13v18h5.5a3.5 3.5 0 0 1 3.5 1.5v-18Z"/>',
                'alert-circle' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v5"/><path d="M12 17h.01"/>',
                'rotate-check' => '<path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/><path d="m9 12 2 2 4-5"/>',
                'x-circle' => '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>',
            ];
            $timeline = [
                ['key' => 'requested', 'label' => 'Pedido realizado', 'date' => $reservation->created_at, 'icon' => 'document-clock', 'active' => true],
                ['key' => 'approved', 'label' => $reservation->status === 'denied' ? 'Pedido recusado' : 'Pedido aprovado', 'date' => $reservation->status === 'denied' ? $reservation->updated_at : $reservation->approved_at, 'icon' => $reservation->status === 'denied' ? 'x-circle' : 'check-circle', 'active' => in_array($reservation->status, ['denied', 'approved', 'in_use', 'extension_pending', 'extended', 'returned'], true) || $reservation->approved_at],
                ['key' => 'in_use', 'label' => 'Em uso', 'date' => $reservation->picked_up_at ?? $reservation->approved_at, 'icon' => 'book-open', 'active' => in_array($reservation->status, ['in_use', 'extension_pending', 'extended', 'returned'], true)],
                ['key' => 'due', 'label' => 'Próximo da devolução', 'date' => ($isDueSoon || $isOverdue) ? $reservation->end_date : null, 'icon' => 'alert-circle', 'active' => $isDueSoon || $isOverdue],
                ['key' => 'returned', 'label' => 'Devolvido', 'date' => $reservation->returned_at, 'icon' => 'rotate-check', 'active' => (bool) $reservation->returned_at],
            ];
            $detailCards = [
                ['Recurso associado', $resource?->title ?? 'Não definido'],
                ['Estado da reserva', $statusLabels[$visualStatus] ?? 'Ativo'],
                ['Data do pedido', optional($reservation->created_at)->format('d/m/Y H:i') ?? 'Não definido'],
                ['Data de aprovação', optional($reservation->approved_at)->format('d/m/Y H:i') ?? 'Pendente'],
                ['Data de entrega', optional($reservation->picked_up_at ?? $reservation->start_date)->format('d/m/Y') ?? 'Não definido'],
                ['Devolução prevista', optional($reservation->end_date)->format('d/m/Y') ?? 'Não definido'],
                ['Dias restantes', $reservation->returned_at ? 'Devolvido' : ($daysRemaining >= 0 ? $daysRemaining . ' dias' : abs($daysRemaining) . ' dias em atraso')],
                ['Cópias disponíveis', $resource?->type === 'digital' ? 'Acesso digital' : (int) ($resource?->quantity_available ?? 0)],
                ['Dono do recurso', $resource?->owner?->name ?? 'Não definido'],
                ['Extensão solicitada', $reservation->extension_reason ?: 'Nenhuma solicitação'],
            ];
        @endphp

        <x-breadcrumbs :items="[['label' => 'Empréstimos ativos', 'url' => route('borrowed')], ['label' => 'Detalhes']]" />

        <section class="mx-auto max-w-6xl" data-reservation-shell>
            <div class="rounded-2xl border border-[#eadfce] bg-white p-5 shadow-[0_18px_50px_rgba(54,39,25,0.08)] dark:border-[#27211a] dark:bg-[#090909] md:p-8">
                @if (session('success') || session('error'))
                    <div class="mb-5 rounded-2xl border px-4 py-3 text-sm font-semibold {{ session('error') ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-100' : 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-100' }}">
                        {{ session('success') ?? session('error') }}
                    </div>
                @endif

                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Gestão de empréstimo</p>
                        <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">{{ $resource?->title ?? 'Reserva sem recurso associado' }}</h1>
                        <p class="mt-2 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">Solicitado por {{ $reservation->user?->name ?? 'utilizador removido' }}</p>
                    </div>
                    <span data-status-badge class="{{ $statusClass }} rounded-full border px-4 py-2 text-xs font-black uppercase tracking-[0.14em]">{{ $statusLabels[$visualStatus] ?? 'Ativo' }}</span>
                </div>

                <div class="mt-8 grid gap-8 lg:grid-cols-[0.78fr_1.22fr] lg:items-start">
                    <div class="rounded-2xl border border-[#eadfce] bg-[#fffaf5] p-5 dark:border-[#332820] dark:bg-[#0f0d0b]">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#9b6b3f]">Linha do tempo</p>
                        <ol class="mt-6 space-y-0">
                            @foreach ($timeline as $index => $step)
                                @php
                                    $done = (bool) $step['active'];
                                    $date = $step['date'];
                                @endphp
                                <li class="relative grid grid-cols-[2.75rem_1fr] gap-3 pb-7 last:pb-0" data-timeline-step="{{ $step['key'] }}">
                                    @if ($index < count($timeline) - 1)
                                        <span class="absolute left-[1.35rem] top-11 h-[calc(100%-2.75rem)] w-px {{ $done ? 'bg-[#FE6807]/55' : 'bg-[#eadfce] dark:bg-[#332820]' }}"></span>
                                    @endif
                                    <span data-step-icon class="relative z-10 flex h-11 w-11 items-center justify-center rounded-2xl border transition duration-300 {{ $done ? 'border-[#FE6807] bg-[#FE6807] text-white shadow-[0_12px_28px_rgba(254,104,7,0.24)]' : 'border-[#eadfce] bg-white text-[#9b6b3f] dark:border-[#332820] dark:bg-black' }}">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$step['icon']] !!}</svg>
                                    </span>
                                    <div class="rounded-2xl border p-4 transition duration-300 hover:-translate-y-0.5 {{ $done ? 'border-[#ffd8bf] bg-white dark:border-[#4a2a1b] dark:bg-black' : 'border-transparent bg-transparent' }}">
                                        <h2 class="text-sm font-black text-[#241b14] dark:text-white">{{ $step['label'] }}</h2>
                                        <p data-step-date class="mt-1 text-xs font-semibold text-[#7f6652] dark:text-[#cfc5ba]">{{ $date ? optional($date)->format('d/m/Y H:i') : 'Pendente' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($detailCards as [$label, $value])
                            <div class="rounded-2xl border border-[#f3e4d8] bg-[#fffaf5] p-4 dark:border-[#332820] dark:bg-[#0f0d0b]">
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#9b6b3f]">{{ $label }}</p>
                                <p class="mt-2 break-words text-sm font-black text-[#241b14] dark:text-white">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-7 flex flex-wrap gap-2">
                    @if ($resource)
                        <a href="{{ $resource->slug ? route('resources.public.show', $resource->slug) : route('resources.show', $resource->id) }}" class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] transition hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#332820] dark:text-[#d8cec3]">Ver recurso</a>
                    @endif

                    @if (! $reservation->returned_at && $isBorrower && $reservation->status !== \App\Models\Reservation::STATUS_EXTENSION_PENDING)
                        <button type="button" data-open-extension class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] transition hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#332820] dark:text-[#d8cec3]">Pedir extensão</button>
                    @endif

                    @if (! $reservation->returned_at && ($isBorrower || $isOwner))
                        <button type="button" data-open-return class="rounded-lg bg-[#FE6807] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#e15f07]">Marcar como devolvido</button>
                    @endif

                    @if (! $reservation->returned_at && $isOwner)
                        @if ($reservation->status === \App\Models\Reservation::STATUS_PENDING)
                            <form method="POST" action="{{ route('reservations.approve', $reservation->id) }}">@csrf @method('PATCH')<button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Aprovar pedido</button></form>
                            <form method="POST" action="{{ route('reservations.deny', $reservation->id) }}">@csrf @method('PATCH')<button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white">Recusar pedido</button></form>
                        @endif
                        @if ($reservation->status === \App\Models\Reservation::STATUS_EXTENSION_PENDING)
                            <form method="POST" action="{{ route('reservations.extension.approve', $reservation->id) }}">@csrf @method('PATCH')<button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Aceitar extensão</button></form>
                            <form method="POST" action="{{ route('reservations.extension.deny', $reservation->id) }}">@csrf @method('PATCH')<button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white">Recusar extensão</button></form>
                        @endif
                        <a href="{{ route('reservations.edit', $reservation->id) }}" class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] transition hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#332820] dark:text-[#d8cec3]">Editar gestão</a>
                    @endif
                </div>
            </div>

            <div data-modal-backdrop class="fixed inset-0 z-50 hidden items-center justify-center bg-black/45 px-4 opacity-0 transition duration-200">
                <section data-extension-modal class="hidden w-full max-w-lg scale-95 rounded-2xl border border-[#eadfce] bg-white p-5 opacity-0 shadow-[0_24px_70px_rgba(20,12,6,0.24)] transition duration-200 dark:border-[#332820] dark:bg-[#0b0908]">
                    <h2 class="text-lg font-black text-[#241b14] dark:text-white">Pedir extensão</h2>
                    <p class="mt-2 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">Indique o motivo para o proprietário avaliar o pedido.</p>
                    <form data-extension-form method="POST" action="{{ route('reservations.extension.request', $reservation->id) }}" class="mt-4 grid gap-3">
                        @csrf
                        @method('PATCH')
                        <textarea name="extension_reason" rows="5" required class="w-full rounded-xl border border-[#decbb8] bg-[#fffaf5] px-4 py-3 text-sm outline-none focus:border-[#FE6807] focus:shadow-[0_0_0_4px_rgba(254,104,7,0.12)] dark:border-[#332820] dark:bg-[#050505] dark:text-white" placeholder="Explique por que precisa de mais tempo"></textarea>
                        <div class="flex flex-wrap justify-end gap-2">
                            <button type="button" data-close-modal class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] dark:border-[#332820] dark:text-[#d8cec3]">Cancelar</button>
                            <button class="rounded-lg bg-[#FE6807] px-4 py-2 text-sm font-semibold text-white">Enviar pedido</button>
                        </div>
                    </form>
                </section>

                <section data-confirm-modal class="hidden w-full max-w-md scale-95 rounded-2xl border border-[#eadfce] bg-white p-5 opacity-0 shadow-[0_24px_70px_rgba(20,12,6,0.24)] transition duration-200 dark:border-[#332820] dark:bg-[#0b0908]">
                    <h2 data-confirm-title class="text-lg font-black text-[#241b14] dark:text-white">Confirmar ação</h2>
                    <p data-confirm-message class="mt-2 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]"></p>
                    <form data-return-form method="POST" action="{{ route('reservations.return', $reservation->id) }}" class="hidden">
                        @csrf
                        @method('PATCH')
                    </form>
                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" data-close-modal class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] dark:border-[#332820] dark:text-[#d8cec3]">Cancelar</button>
                        <button type="button" data-confirm-submit class="rounded-lg bg-[#FE6807] px-4 py-2 text-sm font-semibold text-white">Confirmar</button>
                    </div>
                </section>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const shell = document.querySelector('[data-reservation-shell]');
            if (!shell) return;

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const backdrop = shell.querySelector('[data-modal-backdrop]');
            const extensionModal = shell.querySelector('[data-extension-modal]');
            const confirmModal = shell.querySelector('[data-confirm-modal]');
            const confirmMessage = shell.querySelector('[data-confirm-message]');
            const confirmSubmit = shell.querySelector('[data-confirm-submit]');
            const statusBadge = shell.querySelector('[data-status-badge]');
            let confirmAction = null;

            const toast = (message, type = 'success') => {
                const el = document.createElement('div');
                el.className = `${type === 'error' ? 'border-red-200 bg-red-50 text-red-900 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-100' : 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-100'} fixed right-4 top-24 z-[60] max-w-sm rounded-2xl border p-4 text-sm font-semibold shadow-[0_18px_48px_rgba(54,39,25,0.16)] transition duration-200`;
                el.textContent = message;
                document.body.appendChild(el);
                setTimeout(() => {
                    el.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => el.remove(), 220);
                }, 4200);
            };

            const show = (modal) => {
                backdrop.classList.remove('hidden');
                backdrop.classList.add('flex');
                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    backdrop.classList.remove('opacity-0');
                    modal.classList.remove('scale-95', 'opacity-0');
                });
            };

            const close = () => {
                [extensionModal, confirmModal].forEach((modal) => modal.classList.add('scale-95', 'opacity-0'));
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                    backdrop.classList.remove('flex');
                    extensionModal.classList.add('hidden');
                    confirmModal.classList.add('hidden');
                    confirmAction = null;
                }, 180);
            };

            shell.querySelector('[data-open-extension]')?.addEventListener('click', () => show(extensionModal));
            shell.querySelector('[data-open-return]')?.addEventListener('click', () => {
                confirmMessage.textContent = 'Tem certeza que deseja marcar este recurso como devolvido?';
                confirmAction = 'return';
                show(confirmModal);
            });
            shell.querySelectorAll('[data-close-modal]').forEach((button) => button.addEventListener('click', close));
            backdrop.addEventListener('click', (event) => {
                if (event.target === backdrop) close();
            });

            shell.querySelector('[data-extension-form]')?.addEventListener('submit', async (event) => {
                event.preventDefault();
                confirmMessage.textContent = 'Tem certeza que deseja solicitar extensão deste empréstimo?';
                confirmAction = 'extension';
                extensionModal.classList.add('hidden');
                show(confirmModal);
            });

            confirmSubmit?.addEventListener('click', async () => {
                const form = confirmAction === 'extension'
                    ? shell.querySelector('[data-extension-form]')
                    : shell.querySelector('[data-return-form]');
                if (!form) return;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                        body: new FormData(form),
                    });
                    if (!response.ok) throw new Error('request failed');
                    const data = await response.json();

                    if (statusBadge && data.status_label) statusBadge.textContent = data.status_label;
                    if (confirmAction === 'return') {
                        shell.querySelectorAll('[data-open-return], [data-open-extension]').forEach((button) => button.remove());
                        const returnedStep = shell.querySelector('[data-timeline-step="returned"]');
                        returnedStep?.querySelector('[data-step-icon]')?.classList.add('border-[#FE6807]', 'bg-[#FE6807]', 'text-white', 'shadow-[0_12px_28px_rgba(254,104,7,0.24)]');
                        const returnedDate = returnedStep?.querySelector('[data-step-date]');
                        if (returnedDate) returnedDate.textContent = new Date().toLocaleString('pt-PT');
                    }
                    if (confirmAction === 'extension') {
                        shell.querySelector('[data-open-extension]')?.remove();
                    }
                    toast(data.message || 'Ação concluída com sucesso.');
                    close();
                } catch (error) {
                    toast('Não foi possível concluir a ação.', 'error');
                }
            });
        });
    </script>
</body>
</html>
