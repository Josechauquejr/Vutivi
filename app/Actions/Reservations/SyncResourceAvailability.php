<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;
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
        if ($resource->type === 'digital') {
            $resource->update(['status' => 'available']);
            return;
        }

        $available = (int) $resource->quantity_available;

        $resource->update([
            'status' => $available > 0 ? 'available' : 'reserved',
        ]);
    }
}
