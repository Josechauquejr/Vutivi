<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida a criação de uma nova requisição de recurso.
 */
class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resource_id' => 'required|exists:resources,id',
            'terms_accepted' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'terms_accepted.required' => 'Você deve aceitar os termos e condições',
            'terms_accepted.accepted' => 'Você deve aceitar os termos e condições',
            'resource_id.required' => 'Recurso é obrigatório',
            'resource_id.exists' => 'Recurso não encontrado',
        ];
    }
}
