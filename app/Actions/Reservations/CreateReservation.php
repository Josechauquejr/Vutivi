<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        return DB::transaction(function () use ($reservationData) {
            $resource = Resource::whereKey($reservationData['resource_id'])->lockForUpdate()->firstOrFail();

            if ($resource->type === 'physical' && $this->holdsCopy($reservationData['status'] ?? Reservation::STATUS_APPROVED)) {
                if ((int) $resource->quantity_available <= 0) {
                    throw ValidationException::withMessages([
                        'resource_id' => 'Sem cópias disponíveis.',
                    ]);
                }

                $resource->decrement('quantity_available');
                $resource->refresh();
                $resource->update(['status' => (int) $resource->quantity_available > 0 ? 'available' : 'reserved']);
            }

            return Reservation::create($reservationData);
        });
    }

    private function holdsCopy(string $status): bool
    {
        return in_array($status, Reservation::COPY_HOLDING_STATUSES, true);
    }
}
