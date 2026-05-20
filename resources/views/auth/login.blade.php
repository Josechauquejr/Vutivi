<!DOCTYPE html>
<html lang="pt">
<x-head title="Login" />

<body class="auth-page" style="--auth-background-image: url('{{ asset('img/svg/loginbc.svg') }}')">
    <x-flash-toasts />
    <div class="login-container">
        <picture>
            <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
            <img src="{{ asset('img/png/logo_wb.png') }}" alt="Vutivi Library Logo" class="logo">
        </picture>

        <p class="text-xs font-black uppercase tracking-[0.24em] text-[#9b6b3f]">Acesso seguro</p>
        <h2>Entrar na biblioteca</h2>
        <p class="-mt-3 mb-6 text-sm leading-6 text-[#66594d] dark:text-[#cfc5ba]">Aceda à sua biblioteca para gerir recursos, favoritos, notificações e empréstimos numa experiência segura e organizada.</p>

        <form action="{{ route('login.store') }}" method="POST" class="auth-grid">
            @csrf
            @if (session('error'))
                <p class="form-message form-message--error">{{ session('error') }}</p>
            @endif

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Username</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                    <input class="premium-input" type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Digite seu username" autocomplete="username">
                </span>
                <span class="helper-text">Use o username curto associado a sua conta.</span>
                @error('username')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </label>

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Password</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input class="premium-input" type="password" name="password" id="password" placeholder="Digite sua password" autocomplete="current-password">
                </span>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </label>

            <a href="{{ route('password.request') }}" class="forgot-password">Esqueceu senha?</a>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <p class="register-text">
            Não tem conta? <a href="{{ route('users.create') }}">Inscrever-se</a>
        </p>
    </div>
</body>

</html>
