@if (session('success'))
    <div role="status" style="margin: 1rem 0; padding: 0.75rem 1rem; border: 1px solid #18794e; background: #ecfdf3; color: #14532d;">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div role="alert" style="margin: 1rem 0; padding: 0.75rem 1rem; border: 1px solid #b42318; background: #fef3f2; color: #7a271a;">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div role="alert" style="margin: 1rem 0; padding: 0.75rem 1rem; border: 1px solid #b42318; background: #fef3f2; color: #7a271a;">
        <strong>Corrija os erros abaixo:</strong>

        <ul style="margin: 0.5rem 0 0; padding-left: 1.25rem;">
            @foreach (array_unique($errors->all()) as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
