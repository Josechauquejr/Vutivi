<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Recursos Fisicos</title>
</head>

<body>
    <h1>Recursos Fisicos</h1>
    @include('partials.feedback')

    @forelse ($resources as $resource)
        <article>
            <h2>{{ $resource->title }}</h2>
            <p>{{ $resource->physicalResource->location ?? '-' }}</p>
        </article>
    @empty
        <p>Sem recursos fisicos.</p>
    @endforelse
</body>

</html>
