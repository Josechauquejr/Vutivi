<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VuTivi - Bem-vindo</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-container {
            text-align: center;
            color: white;
            max-width: 600px;
            padding: 2rem;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="welcome-container">
        <h1>Bem-vindo ao VuTivi</h1>
        <p>Sistema de gestão de recursos emprestáveis</p>
        <a href="{{ route('login') }}" class="btn">Entrar</a>
        <a href="{{ route('users.create') }}" class="btn" style="margin-left: 1rem;">Registrar</a>
    </div>
</body>

</html>