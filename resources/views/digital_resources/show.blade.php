<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>{{ $resource->title }}</title>
</head>

<body>
    <h1>{{ $resource->title }}</h1>
    @include('partials.feedback')

    <p>Descricao: {{ $resource->description }}</p>
    <p>Tipo: {{ $resource->type }}</p>
    <p>Estado: {{ $resource->status }}</p>
    <p>Quantidade: {{ $resource->quantity_available }}</p>
    <p>Dono: {{ $resource->owner->name ?? 'Sem dono' }}</p>
    <p>Ficheiro: {{ $resource->digitalResource->file_path ?? '-' }}</p>
    <p>Acesso: {{ $resource->digitalResource->access_type ?? '-' }}</p>
    <p>Dias: {{ $resource->digitalResource->access_days ?? '-' }}</p>

    <p><a href="{{ route('digital-resources.edit', $resource->id) }}">Editar</a></p>

    <h2>Reservas</h2>
    @forelse ($resource->reservations as $reservation)
        <p>{{ $reservation->user->username ?? 'sem usuario' }}: {{ $reservation->start_date?->format('Y-m-d') }} ate {{ $reservation->end_date?->format('Y-m-d') }}</p>
    @empty
        <p>Sem reservas.</p>
    @endforelse
</body>

</html>
