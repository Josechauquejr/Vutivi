<?php

namespace App\Actions\Reservations;

use App\Models\Resource;

/**
 * Recalcula a disponibilidade do recurso a partir do historico real de reservas abertas.
 */
class SyncResourceAvailability
{
    /**
     * Atualiza o status do recurso com base na quantidade de reservas abertas.
     */
    public function handle(Resource $resource): void
    {
        $openReservations = $resource->reservations()->whereNull('returned_at')->count();

        $resource->update([
            'status' => $openReservations >= $resource->quantity_available ? 'reserved' : 'available',
        ]);
    }
}
