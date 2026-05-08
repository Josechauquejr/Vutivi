<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Recursos</title>
</head>

<body>
    <h1>Recursos</h1>
    @include('partials.feedback')

    @forelse ($resources as $resource)
        <article>
            <h2>{{ $resource->title }}</h2>
            <p>Tipo: {{ $resource->type }}</p>
            <p>Estado: {{ $resource->status }}</p>
            <p>Dono: {{ $resource->owner->username ?? '-' }}</p>
        </article>
    @empty
        <p>Sem recursos.</p>
    @endforelse
</body>

</html>
