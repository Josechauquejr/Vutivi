<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Compartilha o contrato de entrada das reservas para que criacao e atualizacao obedeçam as mesmas regras basicas.
 */
abstract class ReservationFormRequest extends FormRequest
{
    /**
     * Autoriza o uso deste request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retorna apenas os campos persistiveis da reserva.
     *
     * @return array<string, mixed>
     */
    public function reservationData(): array
    {
        return $this->only([
            'resource_id',
            'user_id',
            'type',
            'start_date',
            'end_date',
        ]);
    }

    /**
     * Define as regras compartilhadas da reserva.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'resource_id' => ['required', 'exists:resources,id'],
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:physical,digital'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }
}
