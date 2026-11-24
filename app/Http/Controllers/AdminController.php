<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\VolunteerApplication;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ===================================================================
    // DASHBOARD - system overview for the administrator
    // ===================================================================
    public function dashboard()
    {
        // User totals and role breakdown are read directly from the users table.
        $userCounts = User::query()
            ->select('role')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        // Campaign lifecycle counts reflect the statuses used by the organizer portal.
        $campaignStatusCounts = Campaign::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total_users' => User::count(),
            'organizers' => (int) $userCounts->get('organizer', 0),
            'volunteers' => (int) $userCounts->get('volunteer', 0),
            'donors' => (int) $userCounts->get('donor', 0),
            'administrators' => (int) $userCounts->get('administrator', 0),
            'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_campaigns' => Campaign::count(),
            'fundraising_campaigns' => (int) $campaignStatusCounts->get('Fundraising', 0),
            'active_campaigns' => (int) Campaign::whereIn('status', ['Recruiting', 'In Progress'])->count(),
            'completed_campaigns' => (int) $campaignStatusCounts->get('Completed', 0),
            'pending_applications' => VolunteerApplication::where('status', 'Pending')->count(),
            'total_raised' => (float) Donation::where('status', 'Allocated')->sum('amount'),
            'allocated_donations' => Donation::where('status', 'Allocated')->count(),
            'pending_donations' => Donation::where('status', 'Pending')->count(),
        ];

        // Last six calendar months of allocated donations for the funding trend chart.
        $periodStart = now()->startOfMonth()->subMonths(5);
        $monthlyTotals = Donation::query()
            ->where('status', 'Allocated')
            ->where('created_at', '>=', $periodStart)
            ->get(['amount', 'created_at'])
            ->filter(fn ($donation) => $donation->created_at !== null)
            ->groupBy(fn ($donation) => $donation->created_at->format('Y-m'))
            ->map(fn ($donations) => (float) $donations->sum('amount'));

        $donationTrend = collect(range(5, 0))->map(function ($monthsAgo) use ($periodStart, $monthlyTotals) {
            $month = $periodStart->copy()->addMonths(5 - $monthsAgo);
            $key = $month->format('Y-m');

            return [
                'key' => $key,
                'label' => $month->format('M Y'),
                'total' => round((float) $monthlyTotals->get($key, 0), 2),
            ];
        });

        $roleBreakdown = collect(['organizer', 'volunteer', 'donor', 'administrator'])
            ->map(fn ($role) => [
                'label' => ucfirst($role),
                'total' => (int) $userCounts->get($role, 0),
            ]);

        $campaignBreakdown = $campaignStatusCounts
            ->sortKeys()
            ->map(fn ($total, $status) => [
                'label' => $status,
                'total' => (int) $total,
            ])
            ->values();

        $recentDonations = Donation::with([
            'user:id,name',
            'campaign:id,name',
        ])
            ->latest()
            ->take(6)
            ->get();

        $recentCampaigns = Campaign::withCount('volunteers')
            ->latest()
            ->take(5)
            ->get();

        $topCampaigns = Campaign::query()
            ->withSum([
                'donations as collected_amount' => fn ($query) => $query->where('status', 'Allocated'),
            ], 'amount')
            ->orderByDesc('collected_amount')
            ->take(5)
            ->get();

        // The dashboard and Activity Logs page share the same persistent audit trail.
        $recentActivity = ActivityLog::query()
            ->latest()
            ->take(10)
            ->get()
            ->map(function (ActivityLog $log) {
                $icon = match (true) {
                    str_starts_with($log->action, 'user.') => 'manage_accounts',
                    str_starts_with($log->action, 'organization.') => 'verified_user',
                    str_starts_with($log->action, 'notification.') => 'notifications',
                    default => 'history',
                };

                return (object) [
                    'title' => str($log->action)->replace('.', ' ')->headline(),
                    'description' => $log->description ?: ($log->actor_name . ' performed an action.'),
                    'created_at' => $log->created_at,
                    'icon' => $icon,
                    'kind' => 'audit',
                ];
            });

        return view('admin.dashboard', compact(
            'stats',
            'donationTrend',
            'roleBreakdown',
            'campaignBreakdown',
            'recentDonations',
            'recentCampaigns',
            'topCampaigns',
            'recentActivity'
        ));
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

        $user = User::findOrFail($id);
        $actorId = (int) session('user.id');
        $isActive = $request->boolean('is_active');

        if ($user->id === $actorId && (!$isActive || $request->role !== 'administrator')) {
            return back()->with('error', 'You cannot deactivate your own administrator account or change your own role.');
        }

        $oldRole = $user->role;
        $oldActive = (bool) ($user->is_active ?? true);

        $newRole = $request->role;
        $userChanges = [
            'role' => $newRole,
            'is_active' => $isActive,
        ];
        if ($newRole === 'volunteer' && $oldRole !== 'volunteer') {
            $userChanges['volunteer_status'] = 'Active';
        } elseif ($newRole !== 'volunteer' && $oldRole === 'volunteer') {
            $userChanges['volunteer_status'] = null;
        }
        if ($newRole === 'organizer' && $oldRole !== 'organizer') {
            $userChanges['verification_status'] = 'Pending';
            $userChanges['verification_note'] = null;
        }

        $user->update($userChanges);

        ActivityLog::record(
            'user.account_updated',
            "Updated {$user->name}: role {$oldRole} → {$request->role}; account status " . ($oldActive ? 'Active' : 'Suspended') . ' → ' . ($isActive ? 'Active' : 'Suspended')
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

        $oldStatus = $organizer->verification_status ?? 'Pending';
        $organizer->update([
            'verification_status' => $request->verification_status,
            'verification_note' => $request->verification_note,
        ]);

        ActivityLog::record(
            'organization.verification_updated',
            "Changed {$organizer->organization} ({$organizer->name}) from {$oldStatus} to {$request->verification_status}"
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

        $recipientsQuery = User::query()
            ->where('role', $targetRole)
            ->where('is_active', true);

        if ($targetRole === 'volunteer') {
            $recipientsQuery->where(function ($query) {
                $query->whereNull('volunteer_status')
                    ->orWhere('volunteer_status', 'Active');
            });
        }

        $recipients = $recipientsQuery->get();

        DB::transaction(function () use ($request, $recipients, $targetRole) {
            $notification = Notification::create([
                'title'      => $request->title,
                'message'    => $request->message,
                'type'       => 'System',
                'audience'   => $request->audience,
                'status'     => 'Sent',
                'recipients' => $recipients->count(),
            ]);

            // Fan out one user_notification per recipient.
            $now = now();
            $rows = $recipients->map(fn ($recipient) => [
                'user_id' => $recipient->id,
                'notification_id' => $notification->id,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();
            if ($rows) {
                UserNotification::insert($rows);
            }

            ActivityLog::record(
                'notification.broadcast',
                "Sent '{$request->title}' to {$recipients->count()} {$targetRole}(s)"
            );
        });

        return redirect('/admin/notifications')
            ->with('success', "Notification sent to {$recipients->count()} recipient(s).");
    }
}
