<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->user_id
            || (int) $user->id === (int) $reservation->resource?->owner_id;
    }

    public function approve(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->resource?->owner_id
            && $reservation->status === Reservation::STATUS_PENDING;
    }

    public function deny(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->resource?->owner_id
            && $reservation->status === Reservation::STATUS_PENDING;
    }

    public function return(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->user_id
            && $reservation->returned_at === null;
    }

    public function requestExtension(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->user_id
            && $reservation->canExtend()
            && $reservation->status !== Reservation::STATUS_EXTENSION_PENDING;
    }

    public function approveExtension(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->resource?->owner_id
            && $reservation->status === Reservation::STATUS_EXTENSION_PENDING;
    }

    public function denyExtension(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->resource?->owner_id
            && $reservation->status === Reservation::STATUS_EXTENSION_PENDING;
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->resource?->owner_id;
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return (int) $user->id === (int) $reservation->resource?->owner_id;
    }
}
