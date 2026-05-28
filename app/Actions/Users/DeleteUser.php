<?php

namespace App\Actions\Users;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUser
{
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Cancel active reservations of resources this user owns
            $resourceIds = $user->resources()->pluck('id');

            if ($resourceIds->isNotEmpty()) {
                Reservation::whereIn('resource_id', $resourceIds)
                    ->whereIn('status', Reservation::COPY_HOLDING_STATUSES)
                    ->update(['status' => Reservation::STATUS_CANCELLED, 'returned_at' => now()]);

                // Remove resource from other users' favorites
                DB::table('resource_user_favorites')->whereIn('resource_id', $resourceIds)->delete();

                // Delete reviews on user's resources
                DB::table('resource_reviews')->whereIn('resource_id', $resourceIds)->delete();

                // Delete reading list items referencing user's resources
                DB::table('reading_list_items')->whereIn('resource_id', $resourceIds)->delete();

                // Delete reservations of user's resources (already cancelled above, now remove)
                Reservation::whereIn('resource_id', $resourceIds)->forceDelete();

                // Delete physical/digital sub-resource records
                DB::table('physical_resources')->whereIn('resource_id', $resourceIds)->delete();
                DB::table('digital_resources')->whereIn('resource_id', $resourceIds)->delete();
                DB::table('digital_access_logs')->whereIn('resource_id', $resourceIds)->delete();

                // Delete the resources themselves
                $user->resources()->forceDelete();
            }

            // Delete reservations where this user is the borrower
            $user->reservations()->forceDelete();

            // Delete fines
            $user->fines()->delete();

            // Delete reading list items then lists
            $listIds = $user->readingLists()->pluck('id');
            if ($listIds->isNotEmpty()) {
                DB::table('reading_list_items')->whereIn('reading_list_id', $listIds)->delete();
            }
            $user->readingLists()->delete();

            // Delete reviews written by the user
            $user->reviews()->delete();

            // Delete search history
            $user->searchHistory()->delete();

            // Delete digital access logs by the user
            DB::table('digital_access_logs')->where('user_id', $user->id)->delete();

            // Remove from other resources' favorites
            $user->favoriteResources()->detach();

            // Delete moderation notifications
            $user->moderationNotifications()->delete();

            $user->delete();
        });
    }
}
