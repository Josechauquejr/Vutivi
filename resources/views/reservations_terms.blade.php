@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>{{ $resource->title }}</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $resource->description }}</p>

                    <div class="resource-info mt-4 mb-4">
                        <h5>Informações do Recurso</h5>
                        <ul class="list-unstyled">
                            <li><strong>Tipo:</strong> {{ ucfirst($resource->type) }}</li>
                            <li><strong>Dias de Empréstimo:</strong> 
                                {{ $resource->physicalResource->max_loan_days }} dias
                            </li>
                            <li><strong>Localização:</strong> {{ $resource->physicalResource->location }}</li>
                            <li><strong>Condição:</strong> {{ ucfirst($resource->physicalResource->condition) }}</li>
                        </ul>
                    </div>

                    @if($terms)
                        <div class="terms-section alert alert-info">
                            <h5>{{ $terms->title }}</h5>
                            <div class="terms-content" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; border-radius: 4px; margin: 15px 0;">
                                {!! nl2br(e($terms->content)) !!}
                            </div>

                            <form method="POST" action="{{ route('reservations.terms.store') }}" class="mt-4">
                                @csrf

                                <input type="hidden" name="resource_id" value="{{ $resource->id }}">

                                <div class="form-check mb-3">
                                    <input
                                        type="checkbox"
                                        name="terms_accepted"
                                        class="form-check-input"
                                        id="termsCheck"
                                        required
                                    >
                                    <label class="form-check-label" for="termsCheck">
                                        Aceito os termos e condições acima
                                    </label>
                                </div>

                                @error('terms_accepted')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        Aceitar e Solicitar
                                    </button>
                                    <a href="{{ route('resources.show', $resource) }}" class="btn btn-secondary">
                                        Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('reservations.terms.store') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="resource_id" value="{{ $resource->id }}">
                            <input type="hidden" name="terms_accepted" value="1">

                            <p class="text-muted mb-3">Nenhum termo específico definido para este recurso.</p>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Solicitar Recurso
                                </button>
                                <a href="{{ route('resources.show', $resource) }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
