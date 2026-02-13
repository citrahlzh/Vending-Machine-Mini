<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 8);
        $limit = max(1, min($limit, 20));

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notifikasi',
                    'message' => $data['message'] ?? '-',
                    'type' => $data['type'] ?? 'info',
                    'action_url' => $data['action_url'] ?? null,
                    'read_at' => $notification->read_at,
                    'created_at' => optional($notification->created_at)->toIso8601String(),
                    'created_at_human' => optional($notification->created_at)?->diffForHumans(),
                ];
            })
            ->values();

        return response()->json([
            'data' => $notifications,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'count' => $request->user()->unreadNotifications()->count(),
            ],
        ]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ]);
    }
}

