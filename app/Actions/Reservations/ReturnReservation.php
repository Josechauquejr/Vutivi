<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;

/**
 * Marca uma reserva como devolvida sem apagar seu historico.
 */
class ReturnReservation
{
    /**
     * Registra a data e hora de devolucao.
     */
    public function handle(Reservation $reservation): void
    {
        $reservation->update(['returned_at' => now()]);
    }
}
