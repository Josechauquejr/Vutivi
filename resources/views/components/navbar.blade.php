@php
    use App\Models\Resource;
    use App\Models\Reservation;
    use Illuminate\Support\Facades\Schema;

    $baseLinkClass = 'flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-[#4d382b] transition hover:bg-[#fff4ec] hover:text-[#FE6807] dark:text-[#e9ddd2] dark:hover:bg-[#17120f] dark:hover:text-[#ffad77]';
    $activeLinkClass = 'flex items-center gap-2 rounded-xl bg-[#fff1e6] px-3 py-2 text-sm font-semibold text-[#FE6807] ring-1 ring-[#ffd8bf] dark:bg-[#17120f] dark:ring-[#4a2a1b]';
    $user = auth()->user();
    $shortName = '';

    $hasResourceTables = Schema::hasTable('resources') && Schema::hasTable('reservations');

    if ($user && $hasResourceTables) {
        $shortName = $user->username ?: trim(explode(' ', $user->name)[0] ?? 'Perfil');
        $shortName = mb_strlen($shortName) > 14 ? mb_substr($shortName, 0, 14) . '...' : $shortName;
    }

    $initials = $user
        ? collect(explode(' ', trim($user->name ?: $user->username)))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('')
        : '';

    $quickLinks = [
        ['label' => 'Biblioteca', 'route' => 'library', 'icon' => 'book-open'],
        ['label' => 'Favoritos', 'route' => 'favorites', 'icon' => 'heart'],
        ['label' => 'Meus recursos', 'route' => 'mine', 'icon' => 'archive'],
        ['label' => 'Prazos', 'route' => 'loan-alerts', 'icon' => 'clock'],
    ];

    $notifications = collect();

    if ($user) {
        $extensionRequests = Reservation::with(['resource', 'user'])
            ->whereHas('resource', fn ($query) => $query->where('owner_id', $user->id))
            ->whereNull('returned_at')
            ->where('status', Reservation::STATUS_EXTENSION_PENDING)
            ->latest('extension_requested_at')
            ->limit(2)
            ->get();

        foreach ($extensionRequests as $reservation) {
            $notifications->push([
                'title' => 'Pedido de extensao pendente',
                'body' => (optional($reservation->user)->name ?? 'Utilizador') . ' pediu mais tempo para ' . (optional($reservation->resource)->title ?? 'um recurso'),
                'route' => route('reservations.show', $reservation->id),
                'icon' => 'clock',
            ]);
        }

        $extensionDecisions = Reservation::with('resource')
            ->where('user_id', $user->id)
            ->whereNotNull('extension_decision')
            ->whereNotNull('extension_decided_at')
            ->latest('extension_decided_at')
            ->limit(2)
            ->get();

        foreach ($extensionDecisions as $reservation) {
            $approved = $reservation->extension_decision === Reservation::EXTENSION_APPROVED;

            $notifications->push([
                'title' => $approved ? 'Extensao aprovada' : 'Extensao recusada',
                'body' => optional($reservation->resource)->title . ($approved ? ' recebeu um novo prazo.' : ' mantem o prazo original.'),
                'route' => route('reservations.show', $reservation->id),
                'icon' => $approved ? 'check-circle' : 'x-circle',
            ]);
        }

        $dueSoon = Reservation::with('resource')
            ->where('user_id', $user->id)
            ->whereNull('returned_at')
            ->whereIn('status', Reservation::COPY_HOLDING_STATUSES)
            ->whereDate('end_date', '<=', now()->addDays(3)->toDateString())
            ->orderBy('end_date')
            ->limit(2)
            ->get();

        foreach ($dueSoon as $reservation) {
            $notifications->push([
                'title' => 'Prazo próximo de devolução',
                'body' => optional($reservation->resource)->title . ' vence em ' . optional($reservation->end_date)->format('d/m/Y'),
                'route' => route('loan-alerts'),
                'icon' => 'clock',
            ]);
        }

        $approvedLoans = Reservation::with('resource')
            ->where('user_id', $user->id)
            ->whereNotNull('approved_at')
            ->latest('approved_at')
            ->limit(1)
            ->get();

        foreach ($approvedLoans as $reservation) {
            $notifications->push([
                'title' => 'Empréstimo aprovado',
                'body' => optional($reservation->resource)->title ?? 'O seu pedido foi aprovado.',
                'route' => route('borrowed'),
                'icon' => 'check-circle',
            ]);
        }

        $favoriteAvailable = Resource::whereHas('favoritedBy', fn ($query) => $query->where('users.id', $user->id))
            ->where('status', 'available')
            ->latest()
            ->limit(1)
            ->get();

        foreach ($favoriteAvailable as $resource) {
            $notifications->push([
                'title' => 'Favorito disponivel',
                'body' => $resource->title,
                'route' => route('favorites'),
                'icon' => 'heart',
            ]);
        }
    }

    $newResources = $hasResourceTables ? Resource::latest()->limit($user ? 1 : 2)->get() : collect();
    foreach ($newResources as $resource) {
        $notifications->push([
            'title' => 'Novo recurso na biblioteca',
            'body' => $resource->title,
            'route' => route('library', ['q' => $resource->title]),
            'icon' => 'sparkles',
        ]);
    }

    $notifications = $notifications->take(5)->values();
    $notificationCount = $notifications->count();
    $suggestions = $hasResourceTables ? Resource::latest()->limit(6)->get(['title', 'type', 'status']) : collect();
@endphp

<div class="item">
    <nav class="fixed start-0 top-0 z-30 w-full border-b border-[#eee1d6]/90 bg-white/90 backdrop-blur-xl dark:border-[#241915] dark:bg-[#090807]/92">
        <div class="mx-auto grid max-w-screen-2xl grid-cols-[auto_1fr_auto] items-center gap-3 px-3 py-3 sm:px-5">
            <a href="{{ route('index') }}" class="flex min-w-0 items-center gap-3">
                <picture>
                    <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
                    <img src="{{ asset('img/png/logo_wb.png') }}" alt="Logo VuTivi" class="h-9 w-auto sm:h-10" />
                </picture>
            </a>

            <form action="{{ route('library') }}" method="GET" class="navbar-search relative hidden w-full max-w-2xl justify-self-center md:block">
                <label for="navbar-search" class="sr-only">Pesquisar recursos</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9f6a47]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input id="navbar-search" data-smart-search type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar titulo, autor, categoria ou tag"
                        class="min-h-11 w-full rounded-full border border-[#f0decd] bg-[#fffaf6] pl-11 pr-4 text-sm font-medium text-[#2c1c13] outline-none placeholder:text-[#9f816b] focus:border-[#FE6807] focus:bg-white focus:shadow-[0_0_0_4px_rgba(254,104,7,0.10)] dark:border-[#2f241d] dark:bg-[#0d0b09] dark:text-white dark:placeholder:text-[#8d7d70]" />
                </div>
                <div data-search-panel class="invisible absolute left-0 right-0 top-[calc(100%+10px)] max-h-96 origin-top overflow-hidden rounded-2xl border border-[#eadfce] bg-white opacity-0 shadow-[0_24px_70px_rgba(54,39,25,0.16)] transition dark:border-[#2b211b] dark:bg-[#0b0908]">
                    <div class="flex gap-2 border-b border-[#f3e4d8] p-3 dark:border-[#241915]">
                        @foreach (['Todos', 'Digital', 'Físico', 'Popular'] as $tag)
                            <span class="rounded-full bg-[#fff4ec] px-3 py-1 text-xs font-bold text-[#9b5729] dark:bg-[#17120f] dark:text-[#ffb07a]">{{ $tag }}</span>
                        @endforeach
                    </div>
                    <div class="max-h-72 overflow-y-auto p-2">
                        @forelse ($suggestions as $suggestion)
                            <a href="{{ route('library', ['q' => $suggestion->title]) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 hover:bg-[#fff7f0] dark:hover:bg-[#15110e]">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#fff1e6] text-[#FE6807] dark:bg-[#17120f]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-bold text-[#241b14] dark:text-white">{{ $suggestion->title }}</span>
                                    <span class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8a6a4d] dark:text-[#bcae9f]">{{ $suggestion->type }} · {{ $suggestion->status }}</span>
                                </span>
                            </a>
                        @empty
                            <p class="p-4 text-sm font-semibold text-[#7f6652] dark:text-[#cfc5ba]">Comece a digitar para explorar recursos.</p>
                        @endforelse
                    </div>
                </div>
            </form>

            <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                @auth
                    <div class="relative hidden lg:block">
                        <button data-dropdown-toggle="quick-access-menu" data-dropdown-placement="bottom-end" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-[#f0decd] bg-[#fffaf6] px-3 text-sm font-bold text-[#4d382b] hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#2f241d] dark:bg-[#0d0b09] dark:text-[#e9ddd2]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                            Acessos
                        </button>
                        <div id="quick-access-menu" class="z-40 hidden w-64 rounded-2xl border border-[#eadfce] bg-white p-2 shadow-[0_24px_70px_rgba(54,39,25,0.16)] dark:border-[#2b211b] dark:bg-[#0b0908]">
                            @foreach ($quickLinks as $link)
                                <a href="{{ route($link['route']) }}" class="{{ request()->routeIs($link['route']) ? $activeLinkClass : $baseLinkClass }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative">
                        <button data-notification-button data-notification-key="vutivi-notifications-{{ auth()->id() }}-{{ $notificationCount }}" data-dropdown-toggle="notifications-menu" data-dropdown-placement="bottom-end" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#f0decd] bg-[#fffaf6] text-[#4d382b] hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#2f241d] dark:bg-[#0d0b09] dark:text-[#e9ddd2]">
                            <span class="sr-only">Notificações</span>
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            @if ($notificationCount > 0)
                                <span data-notification-badge class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#FE6807] px-1 text-[10px] font-black text-white ring-2 ring-white dark:ring-[#090807]">{{ $notificationCount }}</span>
                            @endif
                        </button>
                        <div id="notifications-menu" class="z-40 hidden w-[min(22rem,calc(100vw-1.5rem))] rounded-2xl border border-[#eadfce] bg-white p-3 shadow-[0_24px_70px_rgba(54,39,25,0.18)] dark:border-[#2b211b] dark:bg-[#0b0908]">
                            <div class="flex items-center justify-between border-b border-[#f3e4d8] pb-3 dark:border-[#241915]">
                                <div>
                                    <p class="text-sm font-black text-[#241b14] dark:text-white">Notificações</p>
                                    <p class="text-xs text-[#806856] dark:text-[#bcae9f]">Prazos, favoritos e novidades</p>
                                </div>
                                <span class="rounded-full bg-[#fff1e6] px-2.5 py-1 text-xs font-black text-[#FE6807] dark:bg-[#17120f]">{{ $notificationCount }}</span>
                            </div>
                            <div class="mt-3 border-b border-[#f3e4d8] pb-2 text-[11px] font-black uppercase tracking-[0.2em] text-[#9b6b3f] dark:border-[#241915]">Hoje</div>
                            <div class="mt-2 max-h-80 overflow-y-auto">
                                @forelse ($notifications as $notification)
                                    <a href="{{ $notification['route'] }}" class="flex gap-3 rounded-xl p-3 hover:bg-[#fff7f0] dark:hover:bg-[#15110e]">
                                        <span class="mt-0.5 flex h-9 w-9 flex-none items-center justify-center rounded-lg bg-[#fff1e6] text-[#FE6807] dark:bg-[#17120f]">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M12 4h9"/><path d="M4 9h16"/><path d="M4 15h16"/></svg>
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-bold text-[#2c1c13] dark:text-white">{{ $notification['title'] }}</span>
                                            <span class="block truncate text-xs leading-5 text-[#806856] dark:text-[#cfc5ba]">{{ $notification['body'] }}</span>
                                            <span class="mt-1 inline-flex rounded-full bg-[#fff1e6] px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.14em] text-[#FE6807] dark:bg-[#17120f]">Não lida</span>
                                        </span>
                                    </a>
                                @empty
                                    <div class="rounded-xl bg-[#fffaf5] p-4 text-sm text-[#806856] dark:bg-[#15110e] dark:text-[#cfc5ba]">Sem alertas pendentes. A biblioteca esta tranquila.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('account') }}" class="hidden min-h-10 items-center gap-2 rounded-full border border-[#f0decd] bg-[#fffaf6] py-1 pl-3 pr-1 text-sm font-bold text-[#2c1c13] dark:border-[#2f241d] dark:bg-[#0d0b09] dark:text-white sm:inline-flex">
                        <span class="max-w-28 truncate">{{ $shortName }}</span>
                        @if ($user->profile_photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo) }}" alt="Foto de perfil" class="h-8 w-8 rounded-full object-cover">
                        @else
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#FE6807] text-xs font-black uppercase text-white">{{ $initials }}</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex min-h-10 items-center rounded-xl bg-[#FE6807] px-4 text-sm font-bold text-white shadow-[0_12px_24px_rgba(254,104,7,0.18)] hover:bg-[#e15f07]">Entrar</a>
                @endauth

                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-[#2c1c13] hover:bg-[#fff4ec] hover:text-[#FE6807] focus:outline-none focus:ring-2 focus:ring-[#FE6807]/30 dark:text-white dark:hover:bg-[#17120f] lg:hidden"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Abrir menu principal</span>
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>

            <div class="col-span-3 hidden w-full lg:hidden" id="navbar-sticky">
                <div class="mt-3 rounded-2xl border border-[#eee1d6] bg-white p-3 shadow-[0_18px_34px_rgba(88,44,14,0.10)] dark:border-[#241915] dark:bg-[#090807]">
                    <form action="{{ route('library') }}" method="GET" class="mb-3 md:hidden">
                        <div class="field-shell">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar recursos" class="premium-input">
                        </div>
                    </form>

                    <ul class="grid gap-1 sm:grid-cols-2">
                        @auth
                            @foreach ($quickLinks as $link)
                                <li><a href="{{ route($link['route']) }}" class="{{ request()->routeIs($link['route']) ? $activeLinkClass : $baseLinkClass }}">{{ $link['label'] }}</a></li>
                            @endforeach
                            <li><a href="{{ route('borrowed') }}" class="{{ request()->routeIs('borrowed') ? $activeLinkClass : $baseLinkClass }}">Empréstimos que fiz</a></li>
                            <li><a href="{{ route('lent') }}" class="{{ request()->routeIs('lent') ? $activeLinkClass : $baseLinkClass }}">Meus empréstimos</a></li>
                            <li><a href="{{ route('account') }}" class="{{ request()->routeIs('account') ? $activeLinkClass : $baseLinkClass }}">Perfil</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm font-semibold text-[#9f3b1f] hover:bg-[#fff4ec] dark:text-[#ffb07a] dark:hover:bg-[#17120f]">Terminar sessao</button>
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('index') }}" class="{{ request()->routeIs('index') ? $activeLinkClass : $baseLinkClass }}">Página inicial</a></li>
                            <li><a href="{{ route('library') }}" class="{{ request()->routeIs('library') ? $activeLinkClass : $baseLinkClass }}">Recursos</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <x-sidebar />
</div>

<x-flash-toasts />
