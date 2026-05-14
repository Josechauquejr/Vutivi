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

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            @if (session('error'))
                <p class="form-message form-message--error">
                    {{ session('error') }}
                </p>
            @endif

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

            <div class="input-group">
                <label for="name">Nome</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Digite seu nome">
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Digite seu email">
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}"
                    placeholder="Digite seu username">
                @error('username')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Digite sua password">
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group">
                <label for="password_confirmation">Confirmar Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Confirme sua password">
            </div>

            <button type="submit" class="btn-login">Registrar</button>
        </form>

        <p class="register-text">
            Já tem conta? <a href="{{ route('login') }}">Entrar</a>
        </p>
    </div>
</body>

</html>
