<!DOCTYPE html>
<html lang="pt">
<x-head title="Login" />

<body class="auth-page" style="--auth-background-image: url('{{ asset('img/svg/loginbc.svg') }}')">

    <div class="login-container">
        <picture>
            <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
            <img src="{{ asset('img/png/logo_wb.png') }}" alt="Vutivi Library Logo" class="logo">
        </picture>

        <h2>Entrar</h2>

        <form action="{{ route('login.store') }}" method="POST">
            @csrf
            @if (session('error'))
                <p style="color: #dc2626; margin-bottom: 12px; font-size: 14px;">
                    {{ session('error') }}
                </p>
            @endif

            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}"
                    placeholder="Digite seu username">
                @error('username')
                    <p style="color: #dc2626; margin-top: 6px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="Digite sua password" ">
                @error('password')
                    <p style=" color: #dc2626; margin-top: 6px;">{{ $message }}</p>
                @enderror
            </div>

            <a href=" {{ route('password.request') }}" class="forgot-password">Esqueceu senha?</a>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <p class="register-text">
            Não tem conta? <a href="{{ route('users.create') }}">Inscrever-se</a>
        </p>
    </div>

</body>

</html>
