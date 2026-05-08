<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Reservas</title>
</head>

<body>
    <h1>Reservas</h1>
    @include('partials.feedback')

    @forelse ($reservations as $reservation)
        <article>
            <h2>{{ $reservation->resource->title ?? '-' }}</h2>
            <p>{{ $reservation->user->username ?? '-' }}</p>
        </article>
    @empty
        <p>Sem reservas.</p>
    @endforelse
</body>

</html>
