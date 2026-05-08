<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Login</title>

</head>

<body>

    <h1>Entrar</h1>
    @include('partials.feedback')

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div>
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
            </div>

        <div>
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required>
        </div>

        <button type="submit">Entrar</button>
    </form>
</body>
