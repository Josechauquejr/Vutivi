<!doctype html>
<html lang="pt">
<x-head title="Minha conta" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Minha conta']]" />
        <section class="mx-auto max-w-5xl space-y-5">

            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-300">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-300">{{ session('error') }}</div>
            @endif

            {{-- Perfil --}}
            <div class="relative overflow-hidden rounded-2xl border border-[#eadfce] bg-white p-6 dark:border-[#27211a] dark:bg-[#090909]">
                <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full bg-[#FE6807]/5 blur-2xl dark:bg-[#FE6807]/10"></div>
                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-start">
                    {{-- Avatar --}}
                    <div class="shrink-0">
                        @if ($user->profile_photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo) }}"
                                 alt="Foto de perfil"
                                 class="h-20 w-20 rounded-full object-cover ring-4 ring-[#fff1e6] dark:ring-[#17120f]">
                        @else
                            @php
                                $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
                            @endphp
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-[#FE6807] text-2xl font-black text-white ring-4 ring-[#fff1e6] dark:ring-[#17120f]">
                                {{ $initials }}
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-bold text-[#241b14] dark:text-white">{{ $user->name }}</h1>
                            @if ($user->isAdmin())
                                <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-black text-red-700 dark:bg-red-950/40 dark:text-red-300">Admin</span>
                            @elseif ($user->isLibrarian())
                                <span class="rounded-full bg-[#fff1e6] px-2.5 py-0.5 text-xs font-black text-[#9b6b3f] dark:bg-[#17120f]">Bibliotecário</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-sm text-[#66594d] dark:text-[#cfc5ba]">{{ $user->email }}</p>
                        <p class="mt-0.5 text-xs text-[#9b8b7c]">
                            @if ($user->username) @{{ $user->username }} · @endif
                            Membro desde {{ $user->created_at->translatedFormat('F Y') }}
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('users.edit') }}"
                               class="inline-flex min-h-9 items-center gap-2 rounded-lg bg-[#FE6807] px-4 text-sm font-bold text-white hover:bg-[#e55c00]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                Editar perfil
                            </a>
                            <a href="{{ route('mine') }}"
                               class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-[#decbb8] px-4 text-sm font-bold text-[#5f4632] hover:border-[#FE6807] hover:text-[#FE6807] dark:border-[#332820] dark:text-[#d8cec3]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8"/><path d="M3 3h18v5H3z"/><path d="M10 12h4"/></svg>
                                Os meus recursos
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="rounded-xl border border-[#eadfce] bg-[#fffaf5] p-4 text-center dark:border-[#27211a] dark:bg-[#0f0d0b]">
                        <p class="text-2xl font-black text-[#9b6b3f]">{{ $stats['resources'] }}</p>
                        <p class="mt-0.5 text-xs font-medium text-[#66594d] dark:text-[#cfc5ba]">Recursos meus</p>
                    </div>
                    <div class="rounded-xl border border-[#eadfce] bg-[#fffaf5] p-4 text-center dark:border-[#27211a] dark:bg-[#0f0d0b]">
                        <p class="text-2xl font-black text-[#9b6b3f]">{{ $stats['borrowed'] }}</p>
                        <p class="mt-0.5 text-xs font-medium text-[#66594d] dark:text-[#cfc5ba]">Em empréstimo</p>
                    </div>
                    <div class="rounded-xl border border-[#eadfce] bg-[#fffaf5] p-4 text-center dark:border-[#27211a] dark:bg-[#0f0d0b]">
                        <p class="text-2xl font-black text-[#9b6b3f]">{{ $stats['lent'] }}</p>
                        <p class="mt-0.5 text-xs font-medium text-[#66594d] dark:text-[#cfc5ba]">Dados a outros</p>
                    </div>
                </div>
            </div>

            {{-- Acesso rápido --}}
            <div class="rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
                <h2 class="text-sm font-black uppercase tracking-[0.18em] text-[#9b6b3f]">Acesso rápido</h2>
                @php
                    $quickLinks = [
                        ['label' => 'Dashboard',        'route' => 'dashboard',           'svg' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'],
                        ['label' => 'Biblioteca',       'route' => 'library',             'svg' => '<path d="M2 4.5A3.5 3.5 0 0 1 5.5 3H11v18H5.5A3.5 3.5 0 0 0 2 22.5v-18Z"/><path d="M22 4.5A3.5 3.5 0 0 0 18.5 3H13v18h5.5a3.5 3.5 0 0 1 3.5 1.5v-18Z"/>'],
                        ['label' => 'Empréstimos',      'route' => 'borrowed',            'svg' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>'],
                        ['label' => 'Histórico',        'route' => 'loan-history',        'svg' => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/><path d="M12 7v5l3 2"/>'],
                        ['label' => 'Favoritos',        'route' => 'favorites',           'svg' => '<path d="m12 21-1.45-1.32C5.4 15.02 2 11.93 2 8.15 2 5.06 4.42 3 7.5 3c1.74 0 3.41.8 4.5 2.05A6.02 6.02 0 0 1 16.5 3C19.58 3 22 5.06 22 8.15c0 3.78-3.4 6.87-8.55 11.53L12 21Z"/>'],
                        ['label' => 'Listas',           'route' => 'reading-lists.index', 'svg' => '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>'],
                        ['label' => 'Multas',           'route' => 'fines.index',         'svg' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>'],
                        ['label' => 'Relatórios',       'route' => 'reports.index',       'svg' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'],
                    ];
                @endphp
                <div class="mt-4 grid grid-cols-4 gap-2">
                    @foreach ($quickLinks as $ql)
                        <a href="{{ route($ql['route']) }}"
                           class="flex items-center gap-2.5 rounded-xl border border-[#eadfce] bg-[#fffaf5] px-3 py-3 text-sm font-semibold text-[#4d382b] transition hover:border-[#FE6807] hover:bg-[#fff1e6] hover:text-[#FE6807] dark:border-[#27211a] dark:bg-[#0f0d0b] dark:text-[#e9ddd2] dark:hover:border-[#FE6807]/40 dark:hover:text-[#FE6807]">
                            <svg class="h-4 w-4 shrink-0 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $ql['svg'] !!}</svg>
                            {{ $ql['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Zona de perigo --}}
            <div class="rounded-2xl border border-red-200 bg-white dark:border-red-900/30 dark:bg-[#090909]">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-50 dark:bg-red-950/30">
                            <svg class="h-4 w-4 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-red-700 dark:text-red-400">Zona de perigo</h2>
                            <p class="mt-0.5 text-sm text-[#66594d] dark:text-[#cfc5ba]">
                                Eliminar a conta é uma ação <strong>permanente e irreversível</strong>.
                                Todos os seus dados serão apagados: recursos, reservas, listas de leitura, avaliações e histórico.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-red-100 dark:border-red-900/20">
                    <details class="group">
                        <summary class="flex cursor-pointer list-none items-center justify-between px-5 py-4 [&::-webkit-details-marker]:hidden">
                            <span class="text-sm font-bold text-red-700 dark:text-red-400">Eliminar a minha conta</span>
                            <svg class="h-4 w-4 text-red-500 transition duration-200 group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>

                        <div class="border-t border-red-100 bg-red-50/50 px-5 py-5 dark:border-red-900/20 dark:bg-red-950/10">
                            @if ($errors->has('current_password'))
                                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 dark:border-red-900/40 dark:bg-red-950/30 dark:text-red-300">
                                    {{ $errors->first('current_password') }}
                                </div>
                            @endif
                            <p class="mb-4 text-sm text-[#66594d] dark:text-[#cfc5ba]">
                                Introduza a sua password atual para confirmar. Esta ação não pode ser desfeita.
                            </p>
                            <form method="POST" action="{{ route('users.destroy') }}"
                                  onsubmit="return confirm('Tem a certeza? Esta ação é permanente e todos os seus dados serão eliminados.')">
                                @csrf
                                @method('DELETE')
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                    <label class="flex-1">
                                        <span class="mb-2 block text-sm font-semibold text-[#241b14] dark:text-white">Password atual</span>
                                        <div class="flex overflow-hidden rounded-xl border border-[#decbb8] bg-[#fffaf5] transition focus-within:border-[#FE6807] focus-within:ring-2 focus-within:ring-[#FE6807]/15 dark:border-[#332820] dark:bg-[#0d0b09]">
                                            <span class="flex shrink-0 items-center border-r border-[#decbb8] px-3.5 text-[#b8a898] dark:border-[#332820] dark:text-[#5a4d42]">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                            </span>
                                            <input type="password" name="current_password" required
                                                   class="min-w-0 flex-1 bg-transparent px-4 py-4 text-sm text-[#241b14] outline-none placeholder:text-[#b8a898] dark:text-white dark:placeholder:text-[#5a4d42]"
                                                   placeholder="A sua password atual" />
                                        </div>
                                    </label>
                                    <button type="submit"
                                            class="inline-flex min-h-[54px] shrink-0 items-center gap-2 rounded-xl bg-red-600 px-6 py-4 text-sm font-bold text-white transition hover:bg-red-700">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        Eliminar permanentemente
                                    </button>
                                </div>
                            </form>
                        </div>
                    </details>
                </div>
            </div>

        </section>
    </main>
</body>
</html>
