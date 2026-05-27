<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Models\ReservationAuditLog;

class ReservationObserver
{
    public function created(Reservation $reservation): void
    {
        ReservationAuditLog::create([
            'reservation_id' => $reservation->id,
            'user_id'        => auth()->id(),
            'event'          => 'created',
            'from_status'    => null,
            'to_status'      => $reservation->status,
        ]);
    }

    public function updated(Reservation $reservation): void
    {
        if (! $reservation->wasChanged('status')) {
            return;
        }

        ReservationAuditLog::create([
            'reservation_id' => $reservation->id,
            'user_id'        => auth()->id(),
            'event'          => 'status_changed',
            'from_status'    => $reservation->getOriginal('status'),
            'to_status'      => $reservation->status,
        ]);
    }

    public function deleted(Reservation $reservation): void
    {
        ReservationAuditLog::create([
            'reservation_id' => $reservation->id,
            'user_id'        => auth()->id(),
            'event'          => 'deleted',
            'from_status'    => $reservation->status,
            'to_status'      => null,
        ]);
    }
}
