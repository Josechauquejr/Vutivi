<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
</head>

<body>
    <h1>Editar Usuario</h1>
    @include('partials.feedback')

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Nome</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div>
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required>
        </div>

        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div>
            <label for="password">Nova senha</label>
            <input id="password" type="password" name="password">
        </div>

        <div>
            <label for="password_confirmation">Confirmar nova senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation">
        </div>

        <button type="submit">Atualizar</button>
    </form>

    <form method="POST" action="{{ route('users.destroy', $user) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Excluir conta</button>
    </form>
</body>

</html>
