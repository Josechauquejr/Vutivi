<?php

namespace App\Actions\Reservations;

use App\Models\Fine;
use App\Models\Reservation;

class IssueFine
{
    public function handle(Reservation $reservation): ?Fine
    {
        if (! $reservation->actual_return_date || ! $reservation->end_date) {
            return null;
        }

        $daysOverdue = (int) $reservation->end_date->diffInDays($reservation->actual_return_date, false);

        if ($daysOverdue <= 0) {
            return null;
        }

        $total = round($daysOverdue * Fine::RATE_PER_DAY, 2);

        return Fine::updateOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'user_id'      => $reservation->user_id,
                'days_overdue' => $daysOverdue,
                'rate_per_day' => Fine::RATE_PER_DAY,
                'total'        => $total,
            ]
        );
    }
}
