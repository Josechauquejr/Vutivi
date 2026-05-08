<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Garante que a reserva respeita o tipo do recurso, a janela de uso e a disponibilidade.
 */
class ValidateReservationAgainstResource
{
    /**
     * Executa as validacoes de negocio da reserva em relacao ao recurso.
     *
     * @param array<string, mixed> $reservationData
     */
    public function handle(Resource $resource, array $reservationData, ?Reservation $currentReservation = null): void
    {
        $this->ensureResourceTypeMatches($resource, (string) $reservationData['type']);
        $this->ensureReservationWindowIsAllowed(
            $resource,
            (string) $reservationData['start_date'],
            (string) $reservationData['end_date'],
        );
        $this->ensureResourceHasAvailability($resource, $currentReservation);
    }

    /**
     * Garante que o tipo informado na reserva corresponde ao tipo real do recurso.
     */
    private function ensureResourceTypeMatches(Resource $resource, string $type): void
    {
        if ($resource->type !== $type) {
            throw ValidationException::withMessages([
                'type' => 'O tipo da reserva nao corresponde ao recurso.',
            ]);
        }
    }

    /**
     * Garante que a data final nao ultrapasse a politica do subtipo do recurso.
     */
    private function ensureReservationWindowIsAllowed(Resource $resource, string $startDate, string $endDate): void
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($end->gt($this->allowedEndDate($resource, $start))) {
            throw ValidationException::withMessages([
                'end_date' => 'A data final ultrapassa o limite permitido para este recurso.',
            ]);
        }
    }

    /**
     * Retorna a ultima data permitida conforme a politica do subtipo concreto.
     */
    private function allowedEndDate(Resource $resource, Carbon $startDate): Carbon
    {
        if ($resource->type === 'physical') {
            return $startDate->copy()->addDays($resource->physicalResource->max_loan_days);
        }

        return $startDate->copy()->addDays($resource->digitalResource->access_days);
    }

    /**
     * Garante que ainda exista vaga disponivel para a reserva.
     */
    private function ensureResourceHasAvailability(Resource $resource, ?Reservation $currentReservation): void
    {
        $openReservations = $resource->reservations()->whereNull('returned_at');

        if ($currentReservation !== null) {
            $openReservations->whereKeyNot($currentReservation->getKey());
        }

        if ($openReservations->count() >= $resource->quantity_available) {
            throw ValidationException::withMessages([
                'resource_id' => 'Sem disponibilidade para este recurso.',
            ]);
        }
    }
}
