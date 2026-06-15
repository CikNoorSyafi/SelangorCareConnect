<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;


class CommunicationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::with('campaign');

        // FILTER TYPE
        if ($request->type) {

            $notifications->where(
                'type',
                $request->type
            );
        }

        $notifications = $notifications
            ->latest()
            ->get();

        // SUMMARY CARDS

        $totalNotifications =
            $notifications->count();

        $systemNotifications =
            $notifications
                ->where('type', 'System')
                ->count();

        $manualNotifications =
            $notifications
                ->where('type', 'Manual')
                ->count();

        $deliveredNotifications =
            $notifications
                ->where('status', 'Delivered')
                ->count();

        foreach ($notifications as $notification) {

            $notification->read_count =
                UserNotification::where(
                    'notification_id',
                    $notification->id
                )
                    ->where(
                        'is_read',
                        true
                    )
                    ->count();

            $notification->recipient_count =
                UserNotification::where(
                    'notification_id',
                    $notification->id
                )
                    ->count();
        }

        return view(
            'organizer.communication.index',
            compact(
                'notifications',
                'totalNotifications',
                'systemNotifications',
                'manualNotifications',
                'deliveredNotifications'
            )
        );
    }

    // CREATE PAGE
    public function create()
    {
        $campaigns = Campaign::where(
            'status',
            'Approved'
        )->get();

        return view(
            'organizer.communication.create',
            compact('campaigns')
        );
    }

    // STORE NOTIFICATION
    public function store(Request $request)
    {
        $message = $request->message;

        if ($request->type == 'System') {

            switch ($request->system_template) {

                case 'Event Reminder':

                    $message =
                        'Reminder: Your registered campaign event starts tomorrow.';
                    break;

                case 'Donation Allocation':

                    $message =
                        'Your donation has been successfully allocated to the selected campaign.';
                    break;

                case 'Campaign Completion':

                    $message =
                        'The selected campaign has been completed successfully.';
                    break;

                case 'Volunteer Assignment':

                    $message =
                        'You have been assigned to a new volunteer campaign.';
                    break;
            }
        }

        $notification = Notification::create([

            'title' =>
                $request->title,

            'message' =>
                $message,

            'type' =>
                $request->type,

            'audience' =>
                $request->audience,

            'campaign_id' =>
                $request->campaign_id,

            'status' =>
                'Sent',

            'recipients' =>
                0,
        ]);

        $users = collect();
        switch ($request->audience) {

            case 'All Volunteers':

                $users =
                    User::where(
                        'role',
                        'volunteer'
                    )->get();

                break;

            case 'All Donors':

                $users =
                    User::where(
                        'role',
                        'donor'
                    )->get();

                break;

            default:

                $users = collect();
        }
        foreach ($users as $user) {

            UserNotification::create([

                'user_id' =>
                    $user->id,

                'notification_id' =>
                    $notification->id,

                'is_read' =>
                    false
            ]);
        }
        $notification->update([

            'recipients' =>
                $users->count()
        ]);

        return redirect('/communication')
            ->with(
                'success',
                'Notification created successfully.'
            );
    }
}