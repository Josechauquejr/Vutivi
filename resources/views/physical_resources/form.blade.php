@php
    $resource = $resource ?? null;
    $physical = $physical ?? null;
@endphp

<div class="grid gap-5 lg:grid-cols-[0.8fr_1.2fr]">
    <label data-upload-zone class="upload-dropzone">
        <span id="physical-cover-preview" class="upload-preview {{ $resource?->cover_image ? '' : 'skeleton' }}">
            <img src="{{ $resource?->cover_image ? \Illuminate\Support\Facades\Storage::url($resource->cover_image) : '' }}" alt="Pré-visualização da capa" class="{{ $resource?->cover_image ? '' : 'hidden' }}">
        </span>
        <span>
            <span class="block text-sm font-black text-[#241b14] dark:text-white">Foto de capa</span>
            <span class="mt-1 block text-xs leading-5 text-[#806856] dark:text-[#bcae9f]">Arraste uma imagem ou clique para selecionar. Ideal para capas de livros e materiais físicos.</span>
        </span>
        <input data-upload-input data-preview-target="#physical-cover-preview" type="file" name="cover_image" accept="image/*" class="sr-only">
    </label>

    <div class="grid gap-5">
        <label>
            <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Titulo</span>
            <span class="field-shell block">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                <input name="title" value="{{ old('title', $resource?->title) }}" required class="premium-input">
            </span>
            <span class="helper-text">Use um título claro e fácil de pesquisar.</span>
        </label>

        <label>
            <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Localização</span>
            <span class="field-shell block">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <input name="location" value="{{ old('location', $physical?->location) }}" required placeholder="Ex: Sala A, Estante 3" class="premium-input">
            </span>
            <span class="helper-text">Ajuda o leitor a encontrar o material físico.</span>
        </label>
    </div>
</div>

<label>
    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Autores</span>
    <span class="field-shell block">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <input name="authors" value="{{ old('authors', $resource?->authors) }}" placeholder="Ex: Eduardo Mondlane; Jose Craveirinha" class="premium-input">
    </span>
    <span class="helper-text">Separe multiplos autores por ponto e virgula.</span>
</label>

<label>
    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Descricao</span>
    <textarea name="description" rows="5" class="w-full rounded-xl border border-[#decbb8] bg-[#fffaf5] px-4 py-3 text-sm outline-none focus:border-[#FE6807] focus:shadow-[0_0_0_4px_rgba(254,104,7,0.12)] dark:border-[#332820] dark:bg-[#050505] dark:text-white">{{ old('description', $resource?->description) }}</textarea>
    <span class="helper-text">Inclua resumo, area de estudo, autor ou observacoes importantes.</span>
</label>

<div class="grid gap-5 sm:grid-cols-3">
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Estado</span>
        <select name="status" class="premium-input">
            @foreach (['available' => 'Disponível', 'reserved' => 'Reservado', 'active' => 'Em uso'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $resource?->status ?? 'available') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Quantidade</span>
        <span class="field-shell block">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M8 12h8"/></svg>
            <input type="number" name="quantity_available" min="1" inputmode="numeric" value="{{ old('quantity_available', $resource?->quantity_available ?? 1) }}" required class="premium-input">
        </span>
    </label>
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Prazo maximo</span>
        <span class="field-shell block">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <input type="number" name="max_loan_days" min="1" inputmode="numeric" value="{{ old('max_loan_days', $physical?->max_loan_days ?? 7) }}" required class="premium-input">
        </span>
    </label>
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Condicao</span>
        <span class="field-shell block">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
            <input name="condition" value="{{ old('condition', $physical?->condition ?? 'good') }}" required placeholder="Ex: bom, novo, usado" class="premium-input">
        </span>
    </label>
    <div class="rounded-xl border border-[#eadfce] bg-[#fffaf5] p-4 text-sm leading-6 text-[#66594d] dark:border-[#332820] dark:bg-[#0f0d0b] dark:text-[#cfc5ba]">
        <strong class="block text-[#241b14] dark:text-white">Fluxo educacional</strong>
        Os utilizadores devem aceitar os termos e condições antes de solicitar o empréstimo.
    </div>
</div>

<button class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#FE6807] px-5 text-sm font-bold text-white shadow-[0_14px_28px_rgba(254,104,7,0.18)] transition hover:-translate-y-0.5 hover:bg-[#e15f07] sm:w-auto">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
    {{ $buttonLabel }}
</button>
