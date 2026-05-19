<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida a extensão do prazo de uma requisição.
 */
class ExtendReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days' => 'required|integer|min:1|max:14',
            'reason' => 'nullable|string|max:300',
            'terms_extension_accepted' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'terms_extension_accepted.required' => 'Você deve aceitar os termos de extensão',
            'terms_extension_accepted.accepted' => 'Você deve aceitar os termos de extensão',
            'days.required' => 'Número de dias é obrigatório',
            'days.integer' => 'Número de dias deve ser um inteiro',
            'days.min' => 'Mínimo de 1 dia',
            'days.max' => 'Máximo de 14 dias',
        ];
    }
}
