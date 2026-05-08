<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Reserva</title>
</head>

<body>
    <h1>Reserva</h1>
    @include('partials.feedback')
    <p>{{ $reservation->resource->title ?? '-' }}</p>
    <p>{{ $reservation->user->username ?? '-' }}</p>
</body>

</html>
