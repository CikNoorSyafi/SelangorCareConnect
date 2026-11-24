<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ActivityLog;
use App\Models\CampaignVolunteer;
use App\Models\Donation;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunicationController extends Controller
{
    /**
     * List only notifications created by the logged-in organizer.
     */
    public function index(Request $request)
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        $allNotificationsQuery = Notification::query()
            ->where('organizer_id', $organizerId);

        $totalNotifications = (clone $allNotificationsQuery)->count();
        $systemNotifications = (clone $allNotificationsQuery)
            ->where('type', 'System')
            ->count();
        $manualNotifications = (clone $allNotificationsQuery)
            ->where('type', 'Manual')
            ->count();
        $sentNotifications = (clone $allNotificationsQuery)
            ->where('status', 'Sent')
            ->count();

        $notificationsQuery = (clone $allNotificationsQuery)
            ->with('campaign')
            ->withCount([
                'userNotifications as recipient_count',
                'userNotifications as read_count' => function ($query) {
                    $query->where('is_read', true);
                },
            ]);

        if ($request->filled('type')) {
            $request->validate([
                'type' => 'in:System,Manual',
            ]);

            $notificationsQuery->where('type', $request->type);
        }

        $notifications = $notificationsQuery
            ->latest()
            ->get();

        return view('organizer.communication.index', compact(
            'notifications',
            'totalNotifications',
            'systemNotifications',
            'manualNotifications',
            'sentNotifications'
        ));
    }

    /** Show the notification creation form with this organizer's campaigns only. */
    public function create()
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        $campaigns = Campaign::query()
            ->where('organizer_id', $organizerId)
            ->orderBy('name')
            ->get();

        return view('organizer.communication.create', compact('campaigns'));
    }

    /**
     * Display the recipient-by-recipient read status for one notification.
     */
    public function show($id)
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        $notification = Notification::query()
            ->where('organizer_id', $organizerId)
            ->with([
                'campaign',
                'userNotifications.user',
            ])
            ->withCount([
                'userNotifications as recipient_count',
                'userNotifications as read_count' => function ($query) {
                    $query->where('is_read', true);
                },
            ])
            ->findOrFail($id);

        $recipients = $notification->userNotifications
            ->sort(function ($a, $b) {
                // Unread recipients first, then sort by recipient name.
                if ((bool) $a->is_read !== (bool) $b->is_read) {
                    return $a->is_read ? 1 : -1;
                }

                return strcasecmp(
                    $a->user->name ?? '',
                    $b->user->name ?? ''
                );
            })
            ->values();

        $unreadCount = max(
            0,
            (int) $notification->recipient_count - (int) $notification->read_count
        );

        return view('organizer.communication.show', compact(
            'notification',
            'recipients',
            'unreadCount'
        ));
    }

    /** Store a notification and create one read-status record per recipient. */
    public function store(Request $request)
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:Manual,System',
            'audience' => 'required|in:All Volunteers,Campaign Volunteers,All Donors,Campaign Donors',
            'campaign_id' => 'nullable|integer',
            'message' => 'required_if:type,Manual|nullable|string',
            'system_template' => 'required_if:type,System|nullable|in:Event Reminder,Donation Allocation,Campaign Completion,Volunteer Assignment',
        ]);

        $campaign = null;
        if (!empty($validated['campaign_id'])) {
            // A submitted campaign must belong to the current organizer.
            $campaign = Campaign::query()
                ->where('organizer_id', $organizerId)
                ->findOrFail($validated['campaign_id']);
        }

        $campaignAudience = in_array($validated['audience'], [
            'Campaign Volunteers',
            'Campaign Donors',
        ], true);

        if ($campaignAudience && !$campaign) {
            return back()
                ->withErrors(['campaign_id' => 'Select one of your campaigns for this audience.'])
                ->withInput();
        }

        $recipientIds = collect();

        switch ($validated['audience']) {
            case 'All Volunteers':
                $recipientIds = User::query()
                    ->where('role', 'volunteer')
                    ->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('volunteer_status')->orWhere('volunteer_status', 'Active');
                    })
                    ->pluck('id');
                break;

            case 'Campaign Volunteers':
                $assignedVolunteerIds = CampaignVolunteer::query()
                    ->where('campaign_id', $campaign->id)
                    ->distinct()
                    ->pluck('volunteer_id');

                $recipientIds = User::query()
                    ->where('role', 'volunteer')
                    ->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('volunteer_status')->orWhere('volunteer_status', 'Active');
                    })
                    ->whereIn('id', $assignedVolunteerIds)
                    ->pluck('id');
                break;

            case 'All Donors':
                $recipientIds = User::query()
                    ->where('role', 'donor')
                    ->where('is_active', true)
                    ->pluck('id');
                break;

            case 'Campaign Donors':
                $campaignDonorIds = Donation::query()
                    ->where('campaign_id', $campaign->id)
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id');

                $recipientIds = User::query()
                    ->where('role', 'donor')
                    ->where('is_active', true)
                    ->whereIn('id', $campaignDonorIds)
                    ->pluck('id');
                break;
        }

        $recipientIds = $recipientIds->unique()->values();

        if ($recipientIds->isEmpty()) {
            return back()
                ->withErrors(['audience' => 'No recipients were found for the selected audience.'])
                ->withInput();
        }

        $message = $validated['message'] ?? '';
        if ($validated['type'] === 'System') {
            $message = match ($validated['system_template']) {
                'Event Reminder' => 'Reminder: Your registered campaign event starts tomorrow.',
                'Donation Allocation' => 'Your donation has been successfully allocated to the selected campaign.',
                'Campaign Completion' => 'The selected campaign has been completed successfully.',
                'Volunteer Assignment' => 'You have been assigned to a new volunteer campaign.',
                default => '',
            };
        }

        DB::transaction(function () use (
            $validated,
            $organizerId,
            $campaign,
            $message,
            $recipientIds
        ) {
            $notification = Notification::create([
                'organizer_id' => $organizerId,
                'title' => $validated['title'],
                'message' => $message,
                'type' => $validated['type'],
                'audience' => $validated['audience'],
                'campaign_id' => $campaign?->id,
                'status' => 'Sent',
                'recipients' => $recipientIds->count(),
            ]);

            $now = now();
            $rows = $recipientIds->map(function ($userId) use ($notification, $now) {
                return [
                    'user_id' => $userId,
                    'notification_id' => $notification->id,
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->all();

            UserNotification::insert($rows);
        });

        ActivityLog::record('communication.notification_sent', "Sent '{$validated['title']}' to {$recipientIds->count()} recipient(s) ({$validated['audience']}).");

        return redirect('/communication')
            ->with('success', 'Notification sent successfully. You can view each recipient’s read status from the notification list.');
    }
}
