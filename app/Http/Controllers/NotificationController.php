<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = (int) session('user.id');
        abort_unless($userId > 0, 403, 'User session not found.');

        $notifications = UserNotification::with('notification')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    /** Display the logged-in volunteer's notifications. */
    public function volunteerIndex()
    {
        $userId = (int) session('user.id');
        abort_unless($userId > 0, 403, 'Volunteer session not found.');

        $notifications = UserNotification::with('notification.campaign')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return view('volunteer.notifications', compact('notifications'));
    }

    /** A recipient can only mark their own notification receipt as read. */
    public function markAsRead($id)
    {
        $userId = (int) session('user.id');
        abort_unless($userId > 0, 403, 'User session not found.');

        $userNotification = UserNotification::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (!$userNotification->is_read) {
            // updated_at is used as the read timestamp in the Organizer report.
            $userNotification->update(['is_read' => true]);
        }

        return back();
    }
}
