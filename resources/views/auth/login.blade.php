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

        <h2>Entrar</h2>

        <form action="{{ route('login.store') }}" method="POST" class="auth-grid">
            @csrf
            @if (session('error'))
                <p class="form-message form-message--error">{{ session('error') }}</p>
            @endif

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Username</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                    <input class="premium-input" type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Digite o seu username" autocomplete="username">
                </span>
                @error('username')<p class="field-error">{{ $message }}</p>@enderror
            </label>

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Palavra-passe</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input class="premium-input" type="password" name="password" id="password" placeholder="Digite a sua palavra-passe" autocomplete="current-password">
                </span>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </label>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-[#241b14] dark:text-white">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-[#7c3aed]">
                    Lembrar-me
                </label>
                <a href="{{ route('password.request') }}" class="forgot-password">Esqueceu a palavra-passe?</a>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <p class="register-text">Não tem conta? <a href="{{ route('users.create') }}">Criar conta</a></p>
    </div>
</body>
</html>
