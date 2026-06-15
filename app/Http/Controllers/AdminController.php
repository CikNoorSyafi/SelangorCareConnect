<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\UserNotification;

class AdminController extends Controller
{
    // ===================================================================
    // DASHBOARD - system overview for the administrator
    // ===================================================================
    public function dashboard()
    {
        $stats = [
            'total_users'     => User::count(),
            'organizers'      => User::where('role', 'organizer')->count(),
            'volunteers'      => User::where('role', 'volunteer')->count(),
            'donors'          => User::where('role', 'donor')->count(),
            'pending_orgs'    => User::where('role', 'organizer')
                ->where('verification_status', 'Pending')
                ->count(),
            'total_campaigns' => Campaign::count(),
            'total_raised'    => Donation::where('status', 'Allocated')->sum('amount'),
        ];

        // Most recent system activity for the dashboard feed.
        $recentLogs = ActivityLog::latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentLogs'));
    }

    // ===================================================================
    // MANAGE USER ROLES - list + update roles / account status
    // ===================================================================
    public function users(Request $request)
    {
        $search = $request->search;
        $role   = $request->role;

        $users = User::query();

        if (!empty($search)) {
            $users->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($role)) {
            $users->where('role', $role);
        }

        $users = $users->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:organizer,donor,volunteer,administrator',
        ]);

        $user    = User::findOrFail($id);
        $oldRole = $user->role;

        $user->update([
            'role'      => $request->role,
            'is_active' => $request->has('is_active'),
        ]);

        ActivityLog::record(
            'user.role_updated',
            "Changed role of {$user->name} from {$oldRole} to {$request->role}"
        );

        return redirect('/admin/users')
            ->with('success', 'User role updated successfully.');
    }

    // ===================================================================
    // VERIFY ORGANIZATION DETAILS - organizer / NGO verification
    // ===================================================================
    public function organizations(Request $request)
    {
        $status = $request->status;

        $organizations = User::where('role', 'organizer');

        if (!empty($status)) {
            $organizations->where('verification_status', $status);
        }

        $organizations = $organizations->latest()->paginate(10)->withQueryString();

        return view('admin.organizations.index', compact('organizations', 'status'));
    }

    public function verifyOrganization(Request $request, $id)
    {
        $request->validate([
            'verification_status' => 'required|in:Pending,Verified,Rejected',
            'verification_note'   => 'nullable|max:500',
        ]);

        $organizer = User::where('role', 'organizer')->findOrFail($id);

        $organizer->update([
            'verification_status' => $request->verification_status,
            'verification_note'   => $request->verification_note,
        ]);

        ActivityLog::record(
            'organization.verified',
            "Set {$organizer->organization} ({$organizer->name}) to {$request->verification_status}"
        );

        return redirect('/admin/organizations')
            ->with('success', 'Organization verification updated.');
    }

    // ===================================================================
    // ACTIVITY LOGS - system audit trail
    // ===================================================================
    public function activityLogs(Request $request)
    {
        $search = $request->search;

        $logs = ActivityLog::query();

        if (!empty($search)) {
            $logs->where(function ($query) use ($search) {
                $query->where('actor_name', 'LIKE', "%{$search}%")
                    ->orWhere('action', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $logs = $logs->latest()->paginate(15)->withQueryString();

        return view('admin.logs.index', compact('logs', 'search'));
    }

    // ===================================================================
    // REPORTS (UC10) - real-time, system-wide report
    // ===================================================================
    public function reports()
    {
        $report = [
            'users_by_role' => [
                'Organizer'     => User::where('role', 'organizer')->count(),
                'Volunteer'     => User::where('role', 'volunteer')->count(),
                'Donor'         => User::where('role', 'donor')->count(),
                'Administrator' => User::where('role', 'administrator')->count(),
            ],
            'campaigns_by_status' => Campaign::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
            'donation_total'   => Donation::where('status', 'Allocated')->sum('amount'),
            'donation_count'   => Donation::where('status', 'Allocated')->count(),
            'pending_orgs'     => User::where('role', 'organizer')
                ->where('verification_status', 'Pending')
                ->count(),
            'top_campaigns'    => Campaign::withSum(
                ['donations as raised' => fn ($q) => $q->where('status', 'Allocated')],
                'amount'
            )
                ->orderByDesc('raised')
                ->take(5)
                ->get(),
        ];

        return view('admin.reports.index', compact('report'));
    }

    // ===================================================================
    // SEND NOTIFICATION (UC09) - broadcast a system announcement
    // ===================================================================
    public function broadcast()
    {
        return view('admin.notifications.create');
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'title'    => 'required|max:255',
            'message'  => 'required',
            'audience' => 'required|in:All Volunteers,All Donors',
        ]);

        // Map the chosen audience to a target role.
        $targetRole = $request->audience === 'All Volunteers' ? 'volunteer' : 'donor';

        $recipients = User::where('role', $targetRole)->get();

        $notification = Notification::create([
            'title'      => $request->title,
            'message'    => $request->message,
            'type'       => 'System',
            'audience'   => $request->audience,
            'status'     => 'Sent',
            'recipients' => $recipients->count(),
        ]);

        // Fan out one user_notification per recipient.
        foreach ($recipients as $recipient) {
            UserNotification::create([
                'user_id'         => $recipient->id,
                'notification_id' => $notification->id,
                'is_read'         => false,
            ]);
        }

        ActivityLog::record(
            'notification.broadcast',
            "Sent '{$request->title}' to {$recipients->count()} {$targetRole}(s)"
        );

        return redirect('/admin/notifications')
            ->with('success', "Notification sent to {$recipients->count()} recipient(s).");
    }
}
