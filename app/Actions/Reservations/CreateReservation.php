<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;

/**
 * Persiste uma nova reserva.
 */
class CreateReservation
{
    /**
     * Cria a reserva com dados ja validados.
     *
     * @param array<string, mixed> $reservationData
     */
    public function handle(array $reservationData): Reservation
    {
        return Reservation::create($reservationData);
    }
}
