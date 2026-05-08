<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>{{ $resource->title }}</title>
</head>

<body>
    <h1>{{ $resource->title }}</h1>
    @include('partials.feedback')
    <p>{{ $resource->physicalResource->location ?? '-' }}</p>
    <p>{{ $resource->physicalResource->condition ?? '-' }}</p>
</body>

</html>
