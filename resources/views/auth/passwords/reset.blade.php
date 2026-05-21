<!DOCTYPE html>
<html lang="pt">
<x-head title="Nova Palavra-passe" />

<body class="auth-page" style="--auth-background-image: url('{{ asset('img/svg/loginbc.svg') }}')">
    <x-flash-toasts />

    <div class="login-container">
        <picture>
            <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
            <img src="{{ asset('img/png/logo_wb.png') }}" alt="Vutivi Library Logo" class="logo">
        </picture>

        <h2>Definir nova palavra-passe</h2>

        <form action="{{ route('password.update') }}" method="POST" class="auth-grid">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Email</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>
                    <input class="premium-input" type="email" name="email" value="{{ old('email', $email) }}" placeholder="Digite o seu email" autocomplete="email" required autofocus>
                </span>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </label>

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Nova palavra-passe</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input class="premium-input" type="password" name="password" placeholder="Digite a nova palavra-passe" autocomplete="new-password" required>
                </span>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </label>

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Confirmar palavra-passe</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                    <input class="premium-input" type="password" name="password_confirmation" placeholder="Repita a nova palavra-passe" autocomplete="new-password" required>
                </span>
            </label>

            <button type="submit" class="btn-login">Guardar nova palavra-passe</button>
        </form>

        <p class="register-text"><a href="{{ route('login') }}">Voltar ao login</a></p>
    </div>
</body>
</html>
