<!doctype html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vutivi Moderation</title>
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css">
    <style>
        @font-face { font-family: "Golos"; src: url("{{ route('font.golos-text') }}") format("truetype"); font-weight: 400 900; }
        @font-face { font-family: "Space Grotesk"; src: url("{{ route('font.space-grotesk') }}") format("truetype"); font-weight: 300 700; }
    </style>
</head>
<body>
    <div id="vutivi-moderation-root"></div>

    <script crossorigin src="https://cdn.jsdelivr.net/npm/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://cdn.jsdelivr.net/npm/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@babel/standalone/babel.min.js"></script>
    @php
        $moderationUser = auth()->user()
            ? [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'username' => auth()->user()->username,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
            ]
            : null;
    @endphp
    <script>
        window.VUTIVI_MODERATION_USER = @json($moderationUser);
    </script>
    <script type="text/babel" src="{{ asset('moderation-app/vutivi-moderation.jsx') }}"></script>
</body>
</html>
