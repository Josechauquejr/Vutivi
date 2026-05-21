<!DOCTYPE html>
<html lang="pt">
<x-head title="Recuperar Palavra-passe" />

<body class="auth-page" style="--auth-background-image: url('{{ asset('img/svg/loginbc.svg') }}')">
    <x-flash-toasts />

    <div class="login-container">
        <picture>
            <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
            <img src="{{ asset('img/png/logo_wb.png') }}" alt="Vutivi Library Logo" class="logo">
        </picture>

        <h2>Recuperar palavra-passe</h2>
        <p class="register-text">Informe o email da sua conta para receber o link de redefinicao.</p>

        <form action="{{ route('password.email') }}" method="POST" class="auth-grid">
            @csrf

            @if (session('status'))
                <p class="form-message form-message--success">{{ session('status') }}</p>
            @endif

            <label class="auth-field">
                <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Email</span>
                <span class="field-shell block">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>
                    <input class="premium-input" type="email" name="email" value="{{ old('email') }}" placeholder="Digite o seu email" autocomplete="email" required autofocus>
                </span>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </label>

            <button type="submit" class="btn-login">Enviar link de redefinicao</button>
        </form>

        <p class="register-text">Lembrou? <a href="{{ route('login') }}">Voltar ao login</a></p>
    </div>
</body>
</html>
