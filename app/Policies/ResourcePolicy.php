<?php

namespace App\Policies;

use App\Models\Resource;
use App\Models\User;

class ResourcePolicy
{
    public function update(User $user, Resource $resource): bool
    {
        return (int) $user->id === (int) $resource->owner_id;
    }

    public function delete(User $user, Resource $resource): bool
    {
        return (int) $user->id === (int) $resource->owner_id;
    }

    public function viewDigital(User $user, Resource $resource): bool
    {
        if ($resource->type !== 'digital') {
            return false;
        }

        if ((int) $user->id === (int) $resource->owner_id) {
            return true;
        }

        return $resource->digitalResource?->access_type === 'public';
    }
}
