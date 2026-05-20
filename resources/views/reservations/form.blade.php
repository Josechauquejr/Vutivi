<div class="grid gap-4 sm:grid-cols-2">
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Recurso</span>
        <select name="resource_id" required class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
            @foreach ($resources as $resource)
                <option value="{{ $resource->id }}" @selected((int) old('resource_id', $reservation?->resource_id) === $resource->id)>{{ $resource->title }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Utilizador</span>
        <select name="user_id" required class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((int) old('user_id', $reservation?->user_id ?? auth()->id()) === $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Tipo</span>
        <select name="type" required class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
            <option value="physical" @selected(old('type', $reservation?->type ?? 'physical') === 'physical')>Fisico</option>
            <option value="digital" @selected(old('type', $reservation?->type) === 'digital')>Digital</option>
        </select>
    </label>
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Inicio</span>
        <input type="date" name="start_date" value="{{ old('start_date', optional($reservation?->start_date)->format('Y-m-d') ?? now()->toDateString()) }}" required class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
    </label>
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Fim</span>
        <input type="date" name="end_date" value="{{ old('end_date', optional($reservation?->end_date)->format('Y-m-d') ?? now()->addDays(7)->toDateString()) }}" required class="min-h-11 w-full rounded-lg border border-[#decbb8] bg-[#fffaf5] px-3 text-sm dark:border-[#332820] dark:bg-[#050505] dark:text-white">
    </label>
</div>
