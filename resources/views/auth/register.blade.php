<!DOCTYPE html>
<html lang="pt">
<x-head title="Registrar" />

<body class="auth-page" style="--auth-background-image: url('{{ asset('img/svg/loginbc.svg') }}')">
    <div class="login-container">
        <picture>
            <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
            <img src="{{ asset('img/png/logo_wb.png') }}" alt="Vutivi Library Logo" class="logo">
        </picture>

        <h2>Registrar</h2>

        <form>
            <div class="input-group">
                <label>Nome</label>
                <input type="text" placeholder="Digite seu nome">
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" placeholder="Digite seu email">
            </div>

            <div class="input-group">
                <label>Username</label>
                <input type="text" placeholder="Digite seu username">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" placeholder="Digite sua password">
            </div>

            <div class="input-group">
                <label>Confirmar Password</label>
                <input type="password" placeholder="Confirme sua password">
            </div>

            <button type="submit" class="btn-login">Registrar</button>
        </form>

        <p class="register-text">
            Já tem conta? <a href="{{ route('login') }}">Entrar</a>
        </p>
    </div>
</body>

</html>
