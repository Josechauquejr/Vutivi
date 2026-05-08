<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>{{ $resource->title }}</title>
</head>

<body>
    <h1>{{ $resource->title }}</h1>
    @include('partials.feedback')
    <p>Tipo: {{ $resource->type }}</p>
    <p>Estado: {{ $resource->status }}</p>
    <p>Quantidade: {{ $resource->quantity_available }}</p>
    <p>Dono: {{ $resource->owner->username ?? '-' }}</p>

    @if ($resource->physicalResource)
        <p>Localizacao: {{ $resource->physicalResource->location }}</p>
    @endif

    @if ($resource->digitalResource)
        <p>Ficheiro: {{ $resource->digitalResource->file_path }}</p>
    @endif

    @forelse ($resource->reservations as $reservation)
        <p>{{ $reservation->user->username ?? '-' }}</p>
    @empty
        <p>Sem reservas.</p>
    @endforelse
</body>

</html>
