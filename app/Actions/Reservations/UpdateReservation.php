<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;

/**
 * Atualiza uma reserva existente.
 */
class UpdateReservation
{
    /**
     * Atualiza a reserva com dados ja validados.
     *
     * @param array<string, mixed> $reservationData
     */
    public function handle(Reservation $reservation, array $reservationData): void
    {
        $reservation->update($reservationData);
    }
}
