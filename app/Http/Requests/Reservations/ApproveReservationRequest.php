<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida a aprovação de uma requisição (apenas para admin).
 */
class ApproveReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Verificado no controller
    }

    public function rules(): array
    {
        return [
            'reason' => 'nullable|string|max:500',
        ];
    }
}
