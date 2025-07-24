<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;

class PruneOldNotificationsAction
{
    public function __invoke(): void
    {
        // Delete all notifications older than 15 days
        DB::table('notifications')
            ->where('created_at', '<', now()->subDays(15))
            ->delete();

        // Get all notifiable entities
        $notifiables = DB::table('notifications')
            ->select('notifiable_type', 'notifiable_id')
            ->groupBy('notifiable_type', 'notifiable_id')
            ->get();

        foreach ($notifiables as $notifiable) {
            // Get the IDs of the 20 most recent notifications for each notifiable
            $recentNotificationIds = DB::table('notifications')
                ->where('notifiable_type', $notifiable->notifiable_type)
                ->where('notifiable_id', $notifiable->notifiable_id)
                ->orderByDesc('created_at')
                ->limit(20)
                ->pluck('id');

            // Delete all notifications for the notifiable except the 20 most recent
            DB::table('notifications')
                ->where('notifiable_type', $notifiable->notifiable_type)
                ->where('notifiable_id', $notifiable->notifiable_id)
                ->whereNotIn('id', $recentNotificationIds)
                ->delete();
        }
    }
}
