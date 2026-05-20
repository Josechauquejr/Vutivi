<!DOCTYPE html>
<html lang="pt">
<x-head title="Registrar" />

<body class="auth-page" style="--auth-background-image: url('{{ asset('img/svg/loginbc.svg') }}')">
    <x-flash-toasts />
    <div class="login-container">
        <picture>
            <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
            <img src="{{ asset('img/png/logo_wb.png') }}" alt="Vutivi Library Logo" class="logo">
        </picture>

        <p class="text-xs font-black uppercase tracking-[0.24em] text-[#9b6b3f]">Nova conta</p>
        <h2>Registrar utilizador</h2>
        <p class="-mt-3 mb-6 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">VUTIVI vem do xitsonga e significa conhecimento. Crie um perfil para partilhar recursos, colaborar e aceder a informação de forma moderna.</p>

        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="auth-grid">
            @csrf
            @if (session('error'))
                <p class="form-message form-message--error">{{ session('error') }}</p>
            @endif

            <label data-upload-zone class="upload-dropzone">
                <span id="register-photo-preview" class="upload-preview skeleton !aspect-square !max-w-32 rounded-full">
                    <img src="" alt="Pré-visualização da foto de perfil" class="hidden">
                </span>
                <span>
                    <span class="block text-sm font-black text-[#241b14] dark:text-white">Foto de perfil</span>
                    <span class="mt-1 block text-xs leading-5 text-[#806856] dark:text-[#bcae9f]">Opcional. Arraste uma imagem ou clique para selecionar.</span>
                </span>
                <input data-upload-input data-preview-target="#register-photo-preview" type="file" name="profile_photo" accept="image/*" class="sr-only">
            </label>

            @if ($errors->any())
                <div class="form-message form-message--error">
                    <strong>Corrija os dados abaixo:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="auth-grid auth-grid--two">
                <label class="auth-field">
                    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Nome</span>
                    <span class="field-shell block">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                        <input class="premium-input" type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Digite seu nome" autocomplete="name">
                    </span>
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </label>

                <label class="auth-field">
                    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Email</span>
                    <span class="field-shell block">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>
                        <input class="premium-input" type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Digite seu email" autocomplete="email">
                    </span>
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </label>
            </div>

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Username</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-8 0v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input class="premium-input" type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Digite seu username" autocomplete="username">
                </span>
                <span class="helper-text">Este nome curto sera usado na navbar.</span>
                @error('username')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </label>

            <div class="auth-grid auth-grid--two">
                <label class="auth-field">
                    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Password</span>
                    <span class="field-shell block">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input class="premium-input" type="password" name="password" id="password" placeholder="Digite sua password" autocomplete="new-password">
                    </span>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </label>

                <label class="auth-field">
                    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Confirmar Password</span>
                    <span class="field-shell block">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                        <input class="premium-input" type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirme sua password" autocomplete="new-password">
                    </span>
                </label>
            </div>

            <button type="submit" class="btn-login">Registrar</button>
        </form>

        <p class="register-text">
            Ja tem conta? <a href="{{ route('login') }}">Entrar</a>
        </p>
    </div>
</body>

</html>
