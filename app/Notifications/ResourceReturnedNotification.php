<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResourceReturnedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $borrower = $this->reservation->user?->name ?? 'Utilizador';
        $title    = $this->reservation->resource?->title ?? 'Recurso';

        return [
            'reservation_id'  => $this->reservation->id,
            'resource_id'     => $this->reservation->resource_id,
            'resource_title'  => $title,
            'borrower_name'   => $borrower,
            'message'         => "{$borrower} devolveu \"{$title}\".",
            'type'            => 'positive',
        ];
    }
}
