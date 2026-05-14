<!doctype html>
<html lang="pt">

<x-head title="Biblioteca de Recursos" />

<body class="home-layout bg-white dark:bg-black">
    <x-navbar />

    <x-resources.resource-detail :resource="$resource" />

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
