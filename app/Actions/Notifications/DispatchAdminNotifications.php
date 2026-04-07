<?php

namespace App\Actions\Notifications;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DispatchAdminNotifications
{
    /**
     * @param  array<string, mixed>  $data
     * @return Collection<int, User>
     */
    public function handle(string $type, string $title, string $message, Model $notifiable, array $data = []): Collection
    {
        $admins = User::query()
            ->whereHas('roles', function ($query): void {
                $query->where('name', 'admin')
                    ->where('guard_name', 'web');
            })
            ->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'notifiable_type' => $notifiable::class,
                'notifiable_id' => $notifiable->getKey(),
                'data' => $data,
            ]);
        }

        return $admins;
    }
}
