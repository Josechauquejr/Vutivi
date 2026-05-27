<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;
use App\Models\Resource;
use App\Notifications\ResourceReturnedNotification;
use Illuminate\Support\Facades\DB;

/**
 * Marca uma reserva como devolvida sem apagar seu historico.
 */
class ReturnReservation
{
    public function __construct(private NotifyWaitlist $notifyWaitlist) {}

    /**
     * Registra a data e hora de devolucao.
     */
    public function handle(Reservation $reservation): void
    {
        $resourceId    = null;
        $returnedReservation = null;

        DB::transaction(function () use ($reservation, &$resourceId, &$returnedReservation) {
            $reservation = Reservation::with('resource.owner', 'user')->whereKey($reservation->id)->lockForUpdate()->firstOrFail();

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

            $returnedReservation = $reservation;

            if ($heldCopy) {
                $resource = Resource::whereKey($reservation->resource_id)->lockForUpdate()->first();

                if ($resource) {
                    $resource->increment('quantity_available');
                    $resource->refresh();
                    $resource->update(['status' => (int) $resource->quantity_available > 0 ? 'available' : 'reserved']);
                    $resourceId = $resource->id;
                }
            }
        });

        if ($returnedReservation) {
            $owner = $returnedReservation->resource?->owner;
            $borrower = $returnedReservation->user;

            if ($owner && $borrower && (int) $owner->id !== (int) $borrower->id) {
                $owner->notify(new ResourceReturnedNotification($returnedReservation));
            }
        }

        if ($resourceId) {
            $this->notifyWaitlist->handle(Resource::findOrFail($resourceId));
        }
    }
}
