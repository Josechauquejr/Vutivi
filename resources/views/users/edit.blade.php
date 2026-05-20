<!doctype html>
<html lang="pt">

<x-head title="Editar perfil" />

<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />

    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <x-breadcrumbs :items="[['label' => 'Perfil', 'url' => route('account')], ['label' => 'Editar perfil']]" />
        <section class="mx-auto max-w-4xl rounded-2xl border border-[#eadfce] bg-white p-6 shadow-[0_18px_50px_rgba(54,39,25,0.08)] dark:border-[#27211a] dark:bg-[#090909] md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#9b6b3f]">Minha conta</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">Editar perfil</h1>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data" class="mt-6 grid gap-5">
                @csrf
                @method('PUT')

                <label data-upload-zone class="upload-dropzone">
                    <span id="profile-photo-preview" class="upload-preview {{ $user->profile_photo ? '' : 'skeleton' }} !aspect-square !max-w-40 rounded-full">
                        <img src="{{ $user->profile_photo ? \Illuminate\Support\Facades\Storage::url($user->profile_photo) : '' }}" alt="Pré-visualização da foto de perfil" class="{{ $user->profile_photo ? '' : 'hidden' }}">
                    </span>
                    <span>
                        <span class="block text-sm font-black text-[#241b14] dark:text-white">Foto de perfil</span>
                        <span class="mt-1 block text-xs leading-5 text-[#806856] dark:text-[#bcae9f]">Arraste uma imagem ou clique para atualizar a sua foto.</span>
                    </span>
                    <input data-upload-input data-preview-target="#profile-photo-preview" type="file" name="profile_photo" accept="image/*" class="sr-only">
                </label>

                <div class="grid gap-4 md:grid-cols-2">
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Nome</span>
                        <input name="name" value="{{ old('name', $user->name) }}" required class="min-h-12 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm outline-none focus:border-[#FE6807] dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Username</span>
                        <input name="username" value="{{ old('username', $user->username) }}" required class="min-h-12 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm outline-none focus:border-[#FE6807] dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                    </label>
                </div>

                <label>
                    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Email</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="min-h-12 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm outline-none focus:border-[#FE6807] dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                </label>

                <div class="grid gap-4 md:grid-cols-2">
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Nova password</span>
                        <input type="password" name="password" autocomplete="new-password" class="min-h-12 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm outline-none focus:border-[#FE6807] dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Confirmar password</span>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="min-h-12 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm outline-none focus:border-[#FE6807] dark:border-[#332820] dark:bg-[#050505] dark:text-white">
                    </label>
                </div>

                <button class="inline-flex min-h-12 items-center justify-center gap-2 rounded-lg bg-[#FE6807] px-5 text-sm font-semibold text-white transition hover:bg-[#e15f07]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                    Guardar alterações
                </button>
            </form>
        </section>
    </main>
</body>

</html>
