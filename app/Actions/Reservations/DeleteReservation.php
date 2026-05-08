<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;

/**
 * Remove uma reserva existente.
 */
class DeleteReservation
{
    /**
     * Exclui a reserva informada.
     */
    public function handle(Reservation $reservation): void
    {
        $reservation->delete();
    }
}
