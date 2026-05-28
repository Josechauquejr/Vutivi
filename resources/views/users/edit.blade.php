<!doctype html>
<html lang="pt">
<x-head title="Editar perfil" />

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Minha conta', 'url' => route('account')], ['label' => 'Editar perfil']]" />
        <section class="mx-auto max-w-3xl space-y-5">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Minha conta</p>
                    <h1 class="mt-0.5 text-2xl font-bold text-[#241b14] dark:text-white">Editar perfil</h1>
                </div>
                <a href="{{ route('account') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#66594d] hover:text-[#FE6807] dark:text-[#cfc5ba]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Voltar
                </a>
            </div>

            @if (session('success'))
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-300">
                    <svg class="mr-1.5 inline h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('users.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Foto de perfil --}}
                <div class="rounded-2xl border border-[#eadfce] bg-white p-6 dark:border-[#27211a] dark:bg-[#090909]">
                    <h2 class="mb-5 text-sm font-black uppercase tracking-[0.18em] text-[#9b6b3f]">Foto de perfil</h2>
                    <label data-upload-zone class="upload-dropzone">
                        <span id="profile-photo-preview"
                            class="upload-preview {{ $user->profile_photo ? '' : 'skeleton' }} !aspect-square !max-w-28 rounded-full">
                            <img src="{{ $user->profile_photo ? \Illuminate\Support\Facades\Storage::url($user->profile_photo) : '' }}"
                                alt="Pré-visualização" class="{{ $user->profile_photo ? '' : 'hidden' }}">
                        </span>
                        <span class="space-y-1">
                            <span class="block text-sm font-bold text-[#241b14] dark:text-white">Alterar foto de
                                perfil</span>
                            <span class="block text-xs leading-relaxed text-[#806856] dark:text-[#bcae9f]">
                                Arraste uma imagem ou clique para selecionar.
                            </span>
                            <span class="block text-xs text-[#9b8b7c]">JPG, PNG ou WebP · Máximo 2 MB</span>
                        </span>
                        <input data-upload-input data-preview-target="#profile-photo-preview" type="file"
                            name="profile_photo" accept="image/*" class="sr-only">
                    </label>
                </div>

                {{-- Informações pessoais --}}
                <div class="rounded-2xl border border-[#eadfce] bg-white p-6 dark:border-[#27211a] dark:bg-[#090909]">
                    <h2 class="mb-6 text-sm font-black uppercase tracking-[0.18em] text-[#9b6b3f]">Informações pessoais
                    </h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        {{-- Nome --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-[#241b14] dark:text-white"
                                for="name">Nome completo</label>
                            <div
                                class="flex overflow-hidden rounded-xl border border-[#decbb8] bg-white transition focus-within:border-[#FE6807] focus-within:ring-2 focus-within:ring-[#FE6807]/15 dark:border-[#332820] dark:bg-[#0d0b09]">
                                <span
                                    class="flex shrink-0 items-center border-r border-[#decbb8] px-3.5 text-[#b8a898] dark:border-[#332820] dark:text-[#5a4d42]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="8" r="4" />
                                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                                    </svg>
                                </span>
                                <input id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    class="min-w-0 flex-1 bg-white px-4 py-4 text-sm text-[#241b14] outline-none placeholder:text-[#b8a898] dark:bg-[#0d0b09] dark:text-white dark:placeholder:text-[#5a4d42]"
                                    placeholder="O seu nome completo">
                            </div>
                        </div>

                        {{-- Username --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#241b14] dark:text-white"
                                for="username">Username</label>
                            <div
                                class="flex overflow-hidden rounded-xl border border-[#decbb8] bg-white transition focus-within:border-[#FE6807] focus-within:ring-2 focus-within:ring-[#FE6807]/15 dark:border-[#332820] dark:bg-[#0d0b09]">
                                <span
                                    class="flex shrink-0 items-center border-r border-[#decbb8] px-3.5 text-sm font-semibold text-[#b8a898] dark:border-[#332820] dark:text-[#5a4d42]">@</span>
                                <input id="username" name="username" value="{{ old('username', $user->username) }}"
                                    required
                                    class="min-w-0 flex-1 bg-white px-4 py-4 text-sm text-[#241b14] outline-none placeholder:text-[#b8a898] dark:bg-[#0d0b09] dark:text-white dark:placeholder:text-[#5a4d42]"
                                    placeholder="o_seu_username">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#241b14] dark:text-white"
                                for="email">Endereço de email</label>
                            <div
                                class="flex overflow-hidden rounded-xl border border-[#decbb8] bg-white transition focus-within:border-[#FE6807] focus-within:ring-2 focus-within:ring-[#FE6807]/15 dark:border-[#332820] dark:bg-[#0d0b09]">
                                <span
                                    class="flex shrink-0 items-center border-r border-[#decbb8] px-3.5 text-sm font-semibold text-[#b8a898] dark:border-[#332820] dark:text-[#5a4d42]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2" />
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                    </svg>
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                    required
                                    class="min-w-0 flex-1 bg-white px-4 py-4 text-sm text-[#241b14] outline-none placeholder:text-[#b8a898] dark:bg-[#0d0b09] dark:text-white dark:placeholder:text-[#5a4d42]"
                                    placeholder="email@exemplo.com">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Alterar password --}}
                <div class="rounded-2xl border border-[#eadfce] bg-white p-6 dark:border-[#27211a] dark:bg-[#090909]">
                    <div class="mb-5">
                        <h2 class="text-sm font-black uppercase tracking-[0.18em] text-[#9b6b3f]">Alterar password</h2>
                        <p class="mt-1.5 text-xs text-[#9b8b7c]">Deixe em branco para manter a password atual.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        {{-- Nova password --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#241b14] dark:text-white"
                                for="password">Nova password</label>
                            <div
                                class="flex overflow-hidden rounded-xl border border-[#decbb8] bg-white transition focus-within:border-[#FE6807] focus-within:ring-2 focus-within:ring-[#FE6807]/15 dark:border-[#332820] dark:bg-[#0d0b09]">
                                <span
                                    class="flex shrink-0 items-center border-r border-[#decbb8] px-3.5 text-[#b8a898] dark:border-[#332820] dark:text-[#5a4d42]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                </span>
                                <input id="password" type="password" name="password" autocomplete="new-password"
                                    class="min-w-0 flex-1 bg-white px-4 py-4 text-sm text-[#241b14] outline-none placeholder:text-[#b8a898] dark:bg-[#0d0b09] dark:text-white dark:placeholder:text-[#5a4d42]"
                                    placeholder="Mínimo 8 caracteres">
                            </div>
                        </div>

                        {{-- Confirmar password --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#241b14] dark:text-white"
                                for="password_confirmation">Confirmar nova password</label>
                            <div
                                class="flex overflow-hidden rounded-xl border border-[#decbb8] bg-white transition focus-within:border-[#FE6807] focus-within:ring-2 focus-within:ring-[#FE6807]/15 dark:border-[#332820] dark:bg-[#0d0b09]">
                                <span
                                    class="flex shrink-0 items-center border-r border-[#decbb8] px-3.5 text-[#b8a898] dark:border-[#332820] dark:text-[#5a4d42]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                                    </svg>
                                </span>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    autocomplete="new-password"
                                    class="min-w-0 flex-1 bg-white px-4 py-4 text-sm text-[#241b14] outline-none placeholder:text-[#b8a898] dark:bg-[#0d0b09] dark:text-white dark:placeholder:text-[#5a4d42]"
                                    placeholder="Repita a nova password">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Guardar --}}
                <div
                    class="flex flex-col gap-3 rounded-2xl border border-[#eadfce] bg-white px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-[#27211a] dark:bg-[#090909]">
                    <p class="text-sm text-[#9b8b7c]">As alterações são aplicadas de imediato após guardar.</p>
                    <button type="submit"
                        class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#FE6807] px-6 text-sm font-bold text-white transition hover:bg-[#e55c00]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                        Guardar alterações
                    </button>
                </div>
            </form>

        </section>
    </main>
</body>

</html>