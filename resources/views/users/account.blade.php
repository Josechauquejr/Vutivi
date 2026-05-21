<!doctype html>
<html lang="pt">
<x-head title="Minha conta" />
<body class="home-layout bg-[#fbfaf7] dark:bg-[#050505]">
    <x-navbar />
    <main class="item px-3 pb-10 pt-4 sm:px-5 md:px-6 lg:px-8">
        <section class="mx-auto max-w-5xl rounded-2xl border border-[#eadfce] bg-white p-5 dark:border-[#27211a] dark:bg-[#090909]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#9b6b3f]">Minha conta</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#241b14] dark:text-white">{{ $user->name }}</h1>
            <p class="mt-2 text-sm text-[#66594d] dark:text-[#cfc5ba]">{{ $user->email }}</p>
            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg bg-[#fffaf5] p-4 dark:bg-[#0f0d0b]"><p class="text-sm text-[#66594d] dark:text-[#cfc5ba]">Recursos meus</p><strong class="text-2xl text-[#241b14] dark:text-white">{{ $stats['resources'] }}</strong></div>
                <div class="rounded-lg bg-[#fffaf5] p-4 dark:bg-[#0f0d0b]"><p class="text-sm text-[#66594d] dark:text-[#cfc5ba]">Emprestimos que fiz</p><strong class="text-2xl text-[#241b14] dark:text-white">{{ $stats['borrowed'] }}</strong></div>
                <div class="rounded-lg bg-[#fffaf5] p-4 dark:bg-[#0f0d0b]"><p class="text-sm text-[#66594d] dark:text-[#cfc5ba]">Meus recursos emprestados</p><strong class="text-2xl text-[#241b14] dark:text-white">{{ $stats['lent'] }}</strong></div>
            </div>
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('users.edit', $user->id) }}" class="rounded-lg bg-[#FE6807] px-4 py-2 text-sm font-semibold text-white">Editar conta</a>
                <a href="{{ route('mine') }}" class="rounded-lg border border-[#decbb8] px-4 py-2 text-sm font-semibold text-[#5f4632] dark:border-[#332820] dark:text-[#d8cec3]">Ver meus recursos</a>
            </div>
        </section>
    </main>
</body>
</html>
