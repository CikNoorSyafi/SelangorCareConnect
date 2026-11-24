<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Donation;
use App\Models\Campaign;

class DonationController extends Controller
{
    // DISPLAY DONATIONS FOR CAMPAIGNS OWNED BY THE CURRENT ORGANIZER
    public function index(Request $request)
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        // Retrieve donations belonging to the logged-in Organizer's campaigns
        $ownedDonations = Donation::query()
            ->with(['campaign', 'user'])
            ->whereHas('campaign', function ($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            });

        // Apply search filter
        $donationQuery = clone $ownedDonations;

        if ($request->filled('search')) {

            $search = $request->search;

            $donationQuery->where(function ($query) use ($search) {

                $query->where('contributor', 'like', "%{$search}%")
                    ->orWhere('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
                        $campaignQuery->where('name', 'like', "%{$search}%");
                    });

            });
        }

        // Apply donation status filter
        if ($request->filled('status')) {

            $donationQuery->where('status', $request->status);

        }

        // Page through real donation records belonging to campaigns owned by this organizer.
        $donations = $donationQuery->latest()->paginate(20)->withQueryString();

        $totalCollections = (float) (clone $ownedDonations)
            ->where('status', 'Allocated')->sum('amount');
        $totalAllocated = (clone $ownedDonations)
            ->where('status', 'Allocated')->count();
        $pendingVerifications = (clone $ownedDonations)
            ->where('status', 'Pending')->count();
        $contributors = (clone $ownedDonations)
            ->whereNotNull('user_id')
            ->distinct()
            ->count('user_id');

        $allocationBySector = (clone $ownedDonations)
            ->where('status', 'Allocated')
            ->get(['campaign_type', 'amount'])
            ->groupBy(fn ($donation) => $donation->campaign_type ?: 'Unclassified')
            ->map(fn ($items, $sector) => [
                'sector' => $sector,
                'amount' => (float) $items->sum('amount'),
                'percentage' => $totalCollections > 0
                    ? round(((float) $items->sum('amount') / $totalCollections) * 100, 1)
                    : 0,
            ])
            ->sortByDesc('amount')
            ->values();

        // Build a stable six-month trend without database-specific MONTH() SQL.
        $firstMonth = now()->startOfMonth()->subMonths(5);
        $monthlyDonationRecords = (clone $ownedDonations)
            ->where('status', 'Allocated')
            ->where('created_at', '>=', $firstMonth)
            ->get(['amount', 'created_at'])
            ->groupBy(function ($donation) {
                return $donation->created_at->format('Y-m');
            });

        $monthlyCollections = [];
        for ($offset = 0; $offset < 6; $offset++) {
            $month = $firstMonth->copy()->addMonths($offset);
            $monthKey = $month->format('Y-m');
            $monthlyCollections[] = [
                'month' => $month->format('M'),
                'amount' => (float) $monthlyDonationRecords
                    ->get($monthKey, collect())
                    ->sum('amount'),
            ];
        }

        return view(
            'organizer.donation.index',
            compact(
                'donations',
                'totalCollections',
                'totalAllocated',
                'pendingVerifications',
                'contributors',
                'allocationBySector',
                'monthlyCollections'
            )
        );
    }


    // SHOW DONATION DETAILS - VIEW PAGE
    public function show($id)
    {
        $donation = Donation::with('user', 'campaign')
            ->whereHas('campaign', function ($query) {
                $query->where('organizer_id', session('user.id'));
            })
            ->findOrFail($id);

        return view(
            'organizer.donation.view',
            compact('donation')
        );
    }

    public function allocationDetails()
    {
        $donations = $this->organizerDonationsQuery()
            ->where('status', 'Allocated')
            ->with('campaign')
            ->latest()
            ->get();

        $allocationGroups = $donations->groupBy(function ($donation) {
            return $donation->campaign?->type ?: ($donation->campaign_type ?: 'Unclassified');
        });

        return view('organizer.donation.allocation-details', compact('allocationGroups'));
    }

    public function exportPdf()
    {
        $donations = Donation::whereHas('campaign', function ($query) {
            $query->where('organizer_id', session('user.id'));
        })->with('campaign', 'user')->get();

        $pdf = PDF::loadView(
            'organizer.donation.report',
            compact('donations')
        );

        return $pdf->download(
            'Donation_Report.pdf'
        );
    }


    private function ownedCampaignQuery()
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        return Campaign::query()->where('organizer_id', $organizerId);
    }
    private function organizerDonationsQuery()
    {
        $organizerId = (int) session('user.id');

        abort_unless($organizerId > 0, 403);

        return Donation::whereHas('campaign', function ($query) use ($organizerId) {

            $query->where('organizer_id', $organizerId);

        });
    }
}