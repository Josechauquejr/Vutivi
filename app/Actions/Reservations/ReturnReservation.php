<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;

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
        DB::transaction(function () use ($reservation) {
            $reservation = Reservation::with('resource')->whereKey($reservation->id)->lockForUpdate()->firstOrFail();

            if ($reservation->returned_at) {
                return;
            }

            $heldCopy = $reservation->resource?->type === 'physical'
                && in_array($reservation->status, Reservation::COPY_HOLDING_STATUSES, true);

            $reservation->update([
                'returned_at' => now(),
                'actual_return_date' => now()->toDateString(),
                'status' => Reservation::STATUS_RETURNED,
            ]);

            if ($heldCopy) {
                $resource = Resource::whereKey($reservation->resource_id)->lockForUpdate()->first();

                if ($resource) {
                    $resource->increment('quantity_available');
                    $resource->refresh();
                    $resource->update(['status' => (int) $resource->quantity_available > 0 ? 'available' : 'reserved']);
                }
            }
        });
    }
}
