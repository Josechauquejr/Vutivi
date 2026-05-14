<!DOCTYPE html>
<html lang="pt">
<x-head title="Esqueceu a Senha" />

<body class="auth-page" style="--auth-background-image: url('{{ asset('img/svg/loginbc.svg') }}')">
    <div class="login-container">
        <picture>
            <source srcset="{{ asset('img/png/logo_bb2.png') }}" media="(prefers-color-scheme: dark)">
            <img src="{{ asset('img/png/logo_wb.png') }}" alt="Vutivi Library Logo" class="logo">
        </picture>

        <h2>Esqueceu a Senha</h2>

        <form>
            <div class="input-group">
                <label>Email</label>
                <input type="email" placeholder="Digite seu email">
            </div>

            <button type="submit" class="btn-login">Enviar Link de Reset</button>
        </form>

        <p class="register-text">
            Lembrou? <a href="{{ route('login') }}">Voltar ao Login</a>
        </p>
    </div>
</body>

</html>
