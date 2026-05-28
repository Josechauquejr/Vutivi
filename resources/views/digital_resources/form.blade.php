@php
    $resource = $resource ?? null;
    $digital = $digital ?? null;
@endphp

<div class="grid gap-5 lg:grid-cols-[1fr_0.9fr]">
    <label data-upload-zone class="upload-dropzone">
        <span id="digital-cover-preview" class="upload-preview {{ $resource?->cover_image ? '' : 'skeleton' }}">
            <img src="{{ $resource?->cover_image ? \Illuminate\Support\Facades\Storage::url($resource->cover_image) : '' }}" alt="Pré-visualização da capa" class="{{ $resource?->cover_image ? '' : 'hidden' }}">
        </span>
        <span>
            <span class="block text-sm font-black text-[#241b14] dark:text-white">Foto de capa</span>
            <span class="mt-1 block text-xs leading-5 text-[#806856] dark:text-[#bcae9f]">Arraste uma imagem ou clique para selecionar.</span>
            <span class="mt-0.5 block text-xs text-[#9b8b7c]">JPG, PNG ou WebP · Máximo 4 MB</span>
        </span>
        <input data-upload-input data-preview-target="#digital-cover-preview" type="file" name="cover_image" accept="image/*" class="sr-only">
    </label>

    <div class="grid gap-5">
        <label>
            <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Ficheiro digital</span>
            <span class="mb-2 block text-xs text-[#9b8b7c]">PDF, Word, Excel, PowerPoint, MP3, MP4, ZIP · Máximo 20 MB</span>
            <input type="file" name="file_path" {{ $digital ? '' : 'required' }} accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp3,.mp4,.mov,.webm,.zip" class="min-h-12 w-full rounded-xl border border-dashed border-[#decbb8] bg-[#fffaf5] px-3 py-3 text-sm outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-[#FE6807] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white focus:border-[#FE6807] focus:shadow-[0_0_0_4px_rgba(254,104,7,0.12)] dark:border-[#332820] dark:bg-[#050505] dark:text-white">
            @if ($digital?->file_path)
                <p class="mt-2 text-xs font-semibold text-[#7f6652] dark:text-[#cfc5ba]">Ficheiro atual carregado.</p>
            @endif
        </label>
    </div>
</div>

<div class="grid gap-5">
    <label>
        <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Título</span>
        <span class="field-shell block">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            <input name="title" value="{{ old('title', $resource?->title) }}" required class="premium-input">
        </span>
    </label>

    <input type="hidden" name="access_days" value="{{ old('access_days', $digital?->access_days ?? 30) }}">
</div>

<label>
    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Descrição</span>
    <textarea name="description" rows="5" class="w-full rounded-xl border border-[#decbb8] bg-[#fffaf5] px-4 py-3 text-sm outline-none focus:border-[#FE6807] focus:shadow-[0_0_0_4px_rgba(254,104,7,0.12)] dark:border-[#332820] dark:bg-[#050505] dark:text-white">{{ old('description', $resource?->description) }}</textarea>
</label>

<label>
    <span class="mb-1 block text-sm font-semibold text-[#241b14] dark:text-white">Autores</span>
    <span class="field-shell block">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <input name="authors" value="{{ old('authors', $resource?->authors) }}" placeholder="Ex: Mia Couto; Paulina Chiziane" class="premium-input">
    </span>
</label>

<div class="grid gap-5 sm:grid-cols-[0.9fr_1.1fr]">
    <fieldset>
        <legend class="mb-2 block text-sm font-semibold text-[#241b14] dark:text-white">Permissão</legend>
        <div class="grid grid-cols-2 gap-2">
            @foreach (['view' => 'Visualizar', 'download' => 'Download'] as $value => $label)
                <label class="flex min-h-12 items-center justify-center rounded-xl border border-[#decbb8] bg-[#fffaf5] text-sm font-bold text-[#5f4632] has-[:checked]:border-[#FE6807] has-[:checked]:bg-[#fff1e6] has-[:checked]:text-[#FE6807] dark:border-[#332820] dark:bg-[#050505] dark:text-[#d8cec3]">
                    <input type="radio" name="access_type" value="{{ $value }}" class="sr-only" @checked(old('access_type', $digital?->access_type ?? 'view') === $value)>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </fieldset>

    <div class="rounded-xl border border-[#eadfce] bg-[#fffaf5] p-4 text-sm leading-6 text-[#66594d] dark:border-[#332820] dark:bg-[#0f0d0b] dark:text-[#cfc5ba]">
        <strong class="block text-[#241b14] dark:text-white">Acesso digital</strong>
        Recursos digitais não usam cópias físicas, quantidade ou devolução. O ficheiro é servido por rotas protegidas.
    </div>
</div>

<button class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#FE6807] px-5 text-sm font-bold text-white shadow-[0_14px_28px_rgba(254,104,7,0.18)] transition hover:-translate-y-0.5 hover:bg-[#e15f07] sm:w-auto">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
    {{ $buttonLabel }}
</button>
