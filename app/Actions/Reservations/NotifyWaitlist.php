<?php

namespace App\Actions\Reservations;

use App\Models\Resource;
use App\Models\ResourceWaitlist;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResourceAvailableNotification;

class NotifyWaitlist
{
    public function handle(Resource $resource): void
    {
        if ($resource->type !== 'physical' || (int) $resource->quantity_available <= 0) {
            return;
        }

        $next = ResourceWaitlist::where('resource_id', $resource->id)
            ->whereNull('notified_at')
            ->orderBy('created_at')
            ->with('user')
            ->first();

        if (! $next) {
            return;
        }

        $next->update(['notified_at' => now()]);

        if ($next->user) {
            $next->user->notify(new ResourceAvailableNotification($resource));
        }
    }
}
