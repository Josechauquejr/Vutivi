<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Criar Usuario</title>
</head>

<body>
    <h1>Criar Usuario</h1>
    @include('partials.feedback')

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div>
            <label for="name">Nome</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <div>
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required>
        </div>

        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div>
            <label for="password_confirmation">Confirmar senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit">Guardar</button>
    </form>
</body>

</html>
