<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Editar {{ $resource->title }}</title>
</head>

<body>
    <h1>Editar Recurso Digital</h1>
    @include('partials.feedback')

    <form method="POST" action="{{ route('digital-resources.update', $resource->id) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="title">Titulo</label>
            <input id="title" type="text" name="title" value="{{ old('title', $resource->title) }}" required>
        </div>

        <div>
            <label for="description">Descricao</label>
            <textarea id="description" name="description">{{ old('description', $resource->description) }}</textarea>
        </div>

        <div>
            <label for="status">Estado</label>
            <select id="status" name="status" required>
                <option value="available" @selected(old('status', $resource->status) === 'available')>available</option>
                <option value="reserved" @selected(old('status', $resource->status) === 'reserved')>reserved</option>
                <option value="active" @selected(old('status', $resource->status) === 'active')>active</option>
            </select>
        </div>

        <div>
            <label for="quantity_available">Quantidade</label>
            <input id="quantity_available" type="number" name="quantity_available" value="{{ old('quantity_available', $resource->quantity_available) }}" min="1" required>
        </div>

        <div>
            <label for="file_path">Caminho do ficheiro</label>
            <input id="file_path" type="text" name="file_path" value="{{ old('file_path', $resource->digitalResource->file_path ?? '') }}" required>
        </div>

        <div>
            <label for="access_type">Tipo de acesso</label>
            <select id="access_type" name="access_type" required>
                <option value="download" @selected(old('access_type', $resource->digitalResource->access_type ?? '') === 'download')>download</option>
                <option value="view" @selected(old('access_type', $resource->digitalResource->access_type ?? '') === 'view')>view</option>
            </select>
        </div>

        <div>
            <label for="access_days">Dias de acesso</label>
            <input id="access_days" type="number" name="access_days" value="{{ old('access_days', $resource->digitalResource->access_days ?? 1) }}" min="1" required>
        </div>

        <button type="submit">Atualizar</button>
    </form>
</body>

</html>
