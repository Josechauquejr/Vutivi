<!doctype html>
<html lang="pt">
<x-head title="Editar empréstimo" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <section class="mx-auto max-w-3xl rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
            <h1 class="text-3xl font-semibold text-[#241b14] dark:text-white">Editar empréstimo</h1>
            @if ($errors->any())
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('reservations.update', $reservation->id) }}" class="mt-6 grid gap-4">
                @csrf
                @method('PUT')
                @include('reservations.form', ['reservation' => $reservation, 'resources' => $resources, 'users' => $users])
                <button class="min-h-11 rounded-lg bg-[#FE6807] px-5 text-sm font-semibold text-white">Atualizar empréstimo</button>
            </form>
        </section>
    </main>
</body>
</html>
