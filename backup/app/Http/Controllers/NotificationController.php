<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications =
            UserNotification::with(
                'notification'
            )
                ->where(
                    'user_id',
                    session('user.id')
                )
                ->latest()
                ->get();

        return view(
            'notifications.index',
            compact('notifications')
        );
    }

    public function markAsRead($id)
    {
        UserNotification::where(
            'id',
            $id
        )->update([

                    'is_read' => true

                ]);

        return back();
    }
}