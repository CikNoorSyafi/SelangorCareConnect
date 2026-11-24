<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignVolunteer;
use App\Models\Donation;
use App\Models\VolunteerApplication;
use Carbon\Carbon;

class OrganizerDashboardController extends Controller
{
    public function index()
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        // Every campaign statistic is scoped to the currently logged-in organizer.
        $ownedCampaigns = Campaign::query()->where('organizer_id', $organizerId);

        $totalCampaigns = (clone $ownedCampaigns)->count();
        $fundraisingCampaigns = (clone $ownedCampaigns)->where('status', 'Fundraising')->count();
        $recruitingCampaigns = (clone $ownedCampaigns)->where('status', 'Recruiting')->count();
        $inProgressCampaigns = (clone $ownedCampaigns)->where('status', 'In Progress')->count();
        $completedCampaigns = (clone $ownedCampaigns)->where('status', 'Completed')->count();
        $activeCampaigns = $recruitingCampaigns + $inProgressCampaigns;

        // Applications and assignments are counted only when their campaign belongs to this organizer.
        $ownedApplications = VolunteerApplication::query()
            ->whereHas('campaign', function ($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            });

        $ownedAssignments = CampaignVolunteer::query()
            ->whereHas('campaign', function ($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            });

        $applicantIds = (clone $ownedApplications)
            ->whereIn('status', ['Pending', 'Approved'])
            ->distinct()
            ->pluck('user_id');
        $assignedVolunteerIds = (clone $ownedAssignments)->distinct()->pluck('volunteer_id');
        $totalVolunteers = $applicantIds->merge($assignedVolunteerIds)->unique()->count();
        $assignedVolunteers = $assignedVolunteerIds->count();

        // Donations are scoped through their campaign, not just by donor or donation date.
        $ownedDonations = Donation::query()
            ->whereHas('campaign', function ($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            });

        $totalAllocatedDonations = (float) (clone $ownedDonations)
            ->where('status', 'Allocated')->sum('amount');
        $pendingDonationCount = (clone $ownedDonations)
            ->where('status', 'Pending')->count();
        $pendingDonationAmount = (float) (clone $ownedDonations)
            ->where('status', 'Pending')->sum('amount');
        $thisMonthAllocatedDonations = (float) (clone $ownedDonations)
            ->where('status', 'Allocated')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $totalApplications = (clone $ownedApplications)->count();
        $pendingApplications = (clone $ownedApplications)->where('status', 'Pending')->count();
        $approvedApplications = (clone $ownedApplications)->where('status', 'Approved')->count();
        $rejectedApplications = (clone $ownedApplications)->where('status', 'Rejected')->count();

        // Six-month allocated-donation trend for this organizer's campaigns only.
        $firstMonth = now()->startOfMonth()->subMonths(5);
        $donationsByMonth = (clone $ownedDonations)
            ->where('status', 'Allocated')
            ->where('created_at', '>=', $firstMonth)
            ->get(['amount', 'created_at'])
            ->groupBy(function ($donation) {
                return Carbon::parse($donation->created_at)->format('Y-m');
            });

        $monthlyDonationLabels = [];
        $monthlyDonationValues = [];

        for ($offset = 0; $offset < 6; $offset++) {
            $month = $firstMonth->copy()->addMonths($offset);
            $monthKey = $month->format('Y-m');

            $monthlyDonationLabels[] = $month->format('M Y');
            $monthlyDonationValues[] = round(
                (float) $donationsByMonth->get($monthKey, collect())->sum('amount'),
                2
            );
        }

        // Highest-funded campaigns owned by this organizer.
        $topCampaigns = (clone $ownedCampaigns)
            ->withSum([
                'donations as allocated_donations_sum' => function ($query) {
                    $query->where('status', 'Allocated');
                },
            ], 'amount')
            ->orderByDesc('allocated_donations_sum')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($campaign) {
                $target = (float) ($campaign->target ?? 0);
                $collected = (float) ($campaign->allocated_donations_sum ?? 0);
                $campaign->funding_progress = $target > 0
                    ? min(100, round(($collected / $target) * 100))
                    : 0;

                return $campaign;
            });

        // Recent records are limited to the current organizer's owned campaigns.
        $recentApplications = (clone $ownedApplications)
            ->with(['user', 'campaign'])
            ->latest()
            ->limit(6)
            ->get();

        $recentDonations = (clone $ownedDonations)
            ->with(['user', 'campaign'])
            ->latest()
            ->limit(6)
            ->get();

        $recentCampaigns = (clone $ownedCampaigns)
            ->latest()
            ->limit(5)
            ->get();

        return view('organizer.dashboard', compact(
            'totalCampaigns',
            'fundraisingCampaigns',
            'recruitingCampaigns',
            'inProgressCampaigns',
            'completedCampaigns',
            'activeCampaigns',
            'totalVolunteers',
            'assignedVolunteers',
            'totalAllocatedDonations',
            'pendingDonationCount',
            'pendingDonationAmount',
            'thisMonthAllocatedDonations',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'monthlyDonationLabels',
            'monthlyDonationValues',
            'topCampaigns',
            'recentApplications',
            'recentDonations',
            'recentCampaigns'
        ));
    }
}
