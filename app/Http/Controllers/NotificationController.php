<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Fetch notifications for the authenticated user
        $notifications = $request->user()->unreadNotifications()
            ->select(
                'id',
                'data',
                'read_at',
                'created_at',
                'updated_at',
            )->limit(config('constants.notification_limit'))->get();

        return response()->json($notifications);
    }
    /**
     * Mark a specific notification as read.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $id)
    {
        // Mark a specific notification as read
        $notification = $request->user()->notifications()->find($id);
        if (empty($notification)) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead(Request $request)
    {
        // Mark all notifications as read
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Get count of unread notifications.
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function unreadCount(Request $request)
    {
        // Get count of unread notifications
        $count = $request->user()->unreadNotifications()->count();

        return response()->json(['unread_count' => $count]);
    }
}
