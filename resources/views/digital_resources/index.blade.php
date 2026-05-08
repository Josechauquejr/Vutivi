<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Recursos Digitais</title>
</head>

<body>
    <h1>Recursos Digitais</h1>
    @include('partials.feedback')

    <p><a href="{{ route('digital-resources.create') }}">Novo recurso digital</a></p>

    @forelse ($resources as $resource)
        <article>
            <h2>{{ $resource->title }}</h2>
            <p>Estado: {{ $resource->status }}</p>
            <p>Dono: {{ $resource->owner->name ?? 'Sem dono' }}</p>
            <p>Acesso: {{ $resource->digitalResource->access_type ?? '-' }}</p>

            <a href="{{ route('digital-resources.show', $resource->id) }}">Ver</a>
            <a href="{{ route('digital-resources.edit', $resource->id) }}">Editar</a>

            <form method="POST" action="{{ route('digital-resources.destroy', $resource->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit">Excluir</button>
            </form>
        </article>
    @empty
        <p>Nenhum recurso digital cadastrado.</p>
    @endforelse

    {{ $resources->links() }}
</body>

</html>
