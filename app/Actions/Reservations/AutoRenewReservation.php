<?php

namespace App\Actions\Reservations;

use App\Models\Reservation;
use App\Models\ResourceWaitlist;
use Illuminate\Validation\ValidationException;

class AutoRenewReservation
{
    public function handle(Reservation $reservation): void
    {
        if (! $reservation->canExtend()) {
            throw ValidationException::withMessages([
                'reservation' => 'Este empréstimo não pode ser renovado.',
            ]);
        }

        $hasWaitlist = ResourceWaitlist::where('resource_id', $reservation->resource_id)
            ->whereNull('notified_at')
            ->exists();

        if ($hasWaitlist) {
            throw ValidationException::withMessages([
                'reservation' => 'Existem utilizadores na fila de espera. A renovação automática não está disponível.',
            ]);
        }

        $reservation->update([
            'status'          => Reservation::STATUS_EXTENDED,
            'extension_count' => ((int) $reservation->extension_count) + 1,
            'end_date'        => $reservation->end_date->copy()->addDays(7)->toDateString(),
            'extension_decision' => Reservation::EXTENSION_APPROVED,
            'extension_decided_at' => now(),
            'extension_decision_note' => 'Renovação automática (sem fila de espera).',
            'extension_reviewed_by' => null,
        ]);
    }
}
