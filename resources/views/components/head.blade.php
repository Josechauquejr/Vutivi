@props(['title' => config('app.name', 'Vutivi')])

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="color-scheme" content="light dark" />
    <title>{{ $title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap"
        rel="stylesheet" />
    <link rel="icon" href="{{ asset('img/png/vuticon_orangeBck@4x.png') }}" type="image/png" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $slot }}
</head>
