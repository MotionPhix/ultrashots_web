<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(): Response
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->paginate(20);

        return Inertia::render('admin/notifications/Index', [
            'notifications' => $notifications,
            'unread_count' => auth()->user()->notifications()->unread()->count(),
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $this->authorize('view', $notification);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->notifications()->unread()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function delete(Notification $notification): JsonResponse
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function getUnreadCount(): JsonResponse
    {
        $count = auth()->user()->notifications()->unread()->count();

        return response()->json(['unread_count' => $count]);
    }
}
