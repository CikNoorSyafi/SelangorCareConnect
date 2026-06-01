<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function index(Request $request)
    {
        // DEFAULT MOCK NOTIFICATIONS
        $defaultNotifications = [

            [
                'id' => 1,
                'title' => 'Flood Relief Volunteer Reminder',

                'message' => 'Reminder: Flood Relief Program starts tomorrow at 8AM.',

                'type' => 'System',

                'audience' => 'Campaign Volunteers',

                'campaign' => 'Selangor Flood Relief 2023',

                'status' => 'Delivered',

                'recipients' => 245,

                'date' => '24 May 2026, 09:00'
            ],

            [
                'id' => 2,
                'title' => 'Thank You Donors',

                'message' => 'Thank you for supporting our education initiative.',

                'type' => 'Manual',

                'audience' => 'All Donors',

                'campaign' => 'Education for All',

                'status' => 'Sent',

                'recipients' => 120,

                'date' => '23 May 2026, 13:20'
            ],

            [
                'id' => 3,
                'title' => 'Volunteer Assignment',

                'message' => 'You have been assigned to Medical Support Campaign.',

                'type' => 'System',

                'audience' => 'Campaign Volunteers',

                'campaign' => 'Medical Support Program',

                'status' => 'Failed',

                'recipients' => 50,

                'date' => '22 May 2026, 11:10'
            ],

            [
                'id' => 4,
                'title' => 'Upcoming Community Event',

                'message' => 'Community clean-up program scheduled next week.',

                'type' => 'Manual',

                'audience' => 'All Volunteers',

                'campaign' => 'Community Development',

                'status' => 'Draft',

                'recipients' => 0,

                'date' => '21 May 2026, 16:45'
            ],

        ];

        // SESSION NOTIFICATIONS
        $sessionNotifications = session('notifications', []);

        // MERGE
        $notifications = collect(array_merge(
            $defaultNotifications,
            $sessionNotifications
        ));

        // FILTER TYPE
        if ($request->type) {

            $notifications = $notifications
                ->where('type', $request->type);
        }

        // SUMMARY CARDS
        $totalNotifications = $notifications->count();

        $systemNotifications = $notifications
            ->where('type', 'System')
            ->count();

        $manualNotifications = $notifications
            ->where('type', 'Manual')
            ->count();

        $deliveredNotifications = $notifications
            ->where('status', 'Delivered')
            ->count();

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
        return view('organizer.communication.create');
    }

    // STORE NOTIFICATION
    public function store(Request $request)
    {
        $notifications = session('notifications', []);

        // AUTO MESSAGE FOR SYSTEM NOTIFICATION
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

        $notifications[] = [

            'id' => count($notifications) + 1,

            'title' => $request->title,

            'message' => $message,

            'type' => $request->type,

            'audience' => $request->audience,

            'campaign' => $request->campaign,

            'status' => $request->status,

            'recipients' => rand(20, 500),

            'date' => now()->format('d M Y, H:i'),
        ];

        session(['notifications' => $notifications]);

        return redirect('/communication')
            ->with(
                'success',
                'Notification sent successfully.'
            );
    }
}