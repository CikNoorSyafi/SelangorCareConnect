<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\SelangorDistricts;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonorController extends Controller
{
    private function donorId(): int
    {
        $userId = (int) session('user.id');
        abort_unless($userId > 0, 403, 'Donor session not found.');

        $user = User::query()->findOrFail($userId);
        abort_unless($user->role === 'donor', 403, 'Donor access required.');

        return $userId;
    }

    private function ownDonationsQuery()
    {
        return Donation::query()->where('user_id', $this->donorId());
    }

    public function dashboard()
    {
        $donorId = $this->donorId();

        // Fundraising is the current lifecycle stage accepting new campaign donations.
        // Approved is retained for records created before the lifecycle status update.
        $campaigns = Campaign::query()
            ->whereIn('status', ['Fundraising', 'Approved'])
            ->withSum([
                'donations as collected_amount' => function ($query) {
                    $query->where('status', 'Allocated');
                },
            ], 'amount')
            ->latest()
            ->take(6)
            ->get()
            ->map(function (Campaign $campaign) {
                $campaign->collected_amount = (float) ($campaign->collected_amount ?? 0);
                $target = (float) ($campaign->target ?? 0);
                $campaign->remaining_amount = max(0, $target - $campaign->collected_amount);
                $campaign->funding_progress = $target > 0
                    ? min(100, round(($campaign->collected_amount / $target) * 100, 1))
                    : 0;

                return $campaign;
            });

        // The map shows all campaigns with a valid Selangor district, not only
        // the six campaigns displayed in the donor dashboard cards.
        $campaignMapPoints = Campaign::query()
            ->whereIn('status', ['Fundraising', 'Recruiting', 'In Progress', 'Completed', 'Approved'])
            ->latest()
            ->get(['id', 'name', 'location', 'status', 'start_date', 'end_date', 'description'])
            ->map(fn (Campaign $campaign) => [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'location' => $campaign->location,
                'district' => SelangorDistricts::resolve($campaign->location),
                'status' => $campaign->status,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'description' => $campaign->description,
            ])->values();

        $history = $this->ownDonationsQuery()
            ->with('campaign')
            ->latest()
            ->take(5)
            ->get();

        $totalContributed = (float) $this->ownDonationsQuery()
            ->where('status', 'Allocated')
            ->sum('amount');

        $campaignsSupported = $this->ownDonationsQuery()
            ->where('status', 'Allocated')
            ->whereNotNull('campaign_id')
            ->distinct()
            ->count('campaign_id');

        // General Fund is shared and has no campaign_id. Only successful donations count.
        $communityFund = (float) Donation::query()
            ->whereNull('campaign_id')
            ->where('status', 'Allocated')
            ->sum('amount');

        return view('donor.dashboard', compact(
            'campaigns',
            'campaignMapPoints',
            'history',
            'communityFund',
            'totalContributed',
            'campaignsSupported'
        ));
    }

    public function campaignDonation(Request $request)
    {
        $donorId = $this->donorId();

        // The dropdown contains every campaign currently eligible to receive donations.
        // Recruiting, In Progress and Completed campaigns are intentionally excluded.
        $campaigns = Campaign::query()
            ->whereIn('status', ['Fundraising', 'Approved'])
            ->withSum([
                'donations as collected_amount' => function ($query) {
                    $query->where('status', 'Allocated');
                },
            ], 'amount')
            ->orderBy('name')
            ->get()
            ->map(function (Campaign $campaign) {
                $campaign->collected_amount = (float) ($campaign->collected_amount ?? 0);
                $target = (float) ($campaign->target ?? 0);
                $campaign->funding_progress = $target > 0
                    ? min(100, round(($campaign->collected_amount / $target) * 100, 1))
                    : 0;

                return $campaign;
            });

        $selectedCampaignId = $request->integer('campaign_id') ?: null;
        if (!$selectedCampaignId && $request->filled('campaign')) {
            $selectedCampaignId = $campaigns->firstWhere('name', $request->input('campaign'))?->id;
        }

        // Preselect a campaign when the donor arrives from a campaign card or map pin.
        if (!$selectedCampaignId || !$campaigns->contains('id', (int) $selectedCampaignId)) {
            $selectedCampaignId = $campaigns->first()?->id;
        }

        $selectedCampaign = $campaigns->firstWhere('id', (int) $selectedCampaignId);
        $campaignSummaryData = $campaigns->mapWithKeys(function (Campaign $campaign) {
            return [(string) $campaign->id => [
                'name' => $campaign->name,
                'location' => $campaign->location ?: 'Location to be confirmed',
                'type' => $campaign->type ?: 'Community Campaign',
                'description' => $campaign->description ?: 'Support this community programme and help its beneficiaries.',
                'start_date' => $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') : 'To be announced',
                'end_date' => $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') : 'To be announced',
                'target' => (float) ($campaign->target ?? 0),
                'collected' => (float) ($campaign->collected_amount ?? 0),
                'progress' => (float) ($campaign->funding_progress ?? 0),
                'status' => $campaign->status === 'Approved' ? 'Open for donations' : $campaign->status,
            ]];
        });

        $donor = User::query()->findOrFail($donorId);
        $donationType = 'campaign';
        $communityFundTotal = null;

        return view('donor.donation.index', compact(
            'campaigns',
            'donationType',
            'selectedCampaignId',
            'selectedCampaign',
            'campaignSummaryData',
            'communityFundTotal',
            'donor'
        ));
    }

    public function generalFund()
    {
        $donorId = $this->donorId();

        $campaigns = collect();
        $donationType = 'general';
        $selectedCampaignId = null;
        $selectedCampaign = null;
        $campaignSummaryData = collect();
        $donor = User::query()->findOrFail($donorId);
        $communityFundTotal = (float) Donation::query()
            ->whereNull('campaign_id')
            ->where('status', 'Allocated')
            ->sum('amount');

        return view('donor.fund.index', compact(
            'campaigns',
            'donationType',
            'selectedCampaignId',
            'selectedCampaign',
            'campaignSummaryData',
            'communityFundTotal',
            'donor'
        ));
    }

    /** Create a pending donation record and open the local payment simulation. */
    public function startPayment(Request $request)
    {
        $donorId = $this->donorId();

        $validated = $request->validate([
            'donation_type' => ['required', 'in:general,campaign'],
            'campaign_id' => ['nullable', 'integer', 'required_if:donation_type,campaign'],
            'amount' => ['required', 'numeric', 'min:1', 'max:99999999.99'],
            'frequency' => ['required', 'in:one_time,monthly'],
            'donor_name' => ['required', 'string', 'max:255'],
            'receipt_email' => ['required', 'email', 'max:255'],
            'dedication' => ['nullable', 'string', 'max:2000'],
        ], [
            'campaign_id.required_if' => 'Please select a campaign to support.',
            'amount.min' => 'The minimum donation amount is RM1.00.',
            'donor_name.required' => 'Please enter the name for your donation receipt.',
            'receipt_email.required' => 'Please enter an email address for your receipt.',
            'frequency.required' => 'Please select a contribution frequency.',
        ]);

        $campaign = null;
        if ($validated['donation_type'] === 'campaign') {
            $campaign = Campaign::query()
                ->whereIn('status', ['Fundraising', 'Approved'])
                ->findOrFail((int) $validated['campaign_id']);
        }

        $user = User::query()->findOrFail($donorId);
        do {
            $transactionId = 'SCC-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8));
        } while (Donation::query()->where('transaction_id', $transactionId)->exists());

        $donation = DB::transaction(function () use (
            $donorId,
            $campaign,
            $validated,
            $transactionId
        ) {
            return Donation::create([
                'user_id' => $donorId,
                'campaign_id' => $campaign?->id,
                'contributor' => trim($validated['donor_name']),
                'transaction_id' => $transactionId,
                'campaign_type' => $campaign?->type ?? 'Community Fund',
                'amount' => number_format((float) $validated['amount'], 2, '.', ''),
                'payment_method' => 'Not selected',
                'receipt_no' => 'RCP-' . Str::upper(Str::random(12)),
                'frequency' => $validated['frequency'],
                'receipt_email' => strtolower(trim($validated['receipt_email'])),
                'dedication' => isset($validated['dedication']) ? trim($validated['dedication']) : null,
                'status' => 'Pending',
            ]);
        });

        ActivityLog::record('donation.payment_started', "Started a donation of RM {$donation->amount} for " . ($donation->campaign?->name ?? 'General Fund') . '.');
        return redirect()->route('donor.payment.gateway', $donation->id);
    }

    public function paymentGateway(int $id)
    {
        $donation = $this->ownDonationsQuery()
            ->with('campaign')
            ->findOrFail($id);

        if ($donation->status !== 'Pending') {
            return redirect()->route('donor.payment.confirmation', $donation->id)
                ->with('info', 'This transaction has already been processed.');
        }

        return view('donor.payment.gateway', compact('donation'));
    }

    public function paymentSuccess(Request $request, int $id)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:card,fpx,wallet'],
        ]);

        return $this->finishPayment($id, 'Allocated', $validated['payment_method']);
    }

    public function paymentFailed(Request $request, int $id)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:card,fpx,wallet'],
        ]);

        return $this->finishPayment($id, 'Failed', $validated['payment_method']);
    }

    private function finishPayment(int $id, string $result, string $paymentMethod)
    {
        $donorId = $this->donorId();

        // Lock the donor-owned row so repeated submissions cannot settle it twice.
        $paymentMethodLabel = match ($paymentMethod) {
            'card' => 'Credit / Debit Card',
            'fpx' => 'FPX Online Banking',
            'wallet' => 'E-Wallet',
        };

        $wasProcessed = DB::transaction(function () use ($id, $donorId, $result, $paymentMethodLabel) {
            $donation = Donation::query()
                ->where('user_id', $donorId)
                ->lockForUpdate()
                ->findOrFail($id);

            if ($donation->status !== 'Pending') {
                return false;
            }

            $donation->update([
                'status' => $result,
                'payment_method' => $paymentMethodLabel,
            ]);
            return true;
        });

        if (!$wasProcessed) {
            return redirect()->route('donor.payment.confirmation', $id)
                ->with('info', 'This transaction has already been processed.');
        }

        $donation = Donation::query()->where('user_id', $donorId)->findOrFail($id);
        ActivityLog::record('donation.payment_processed', "A donation of RM {$donation->amount} for " . ($donation->campaign?->name ?? 'General Fund') . " was marked {$result}.");

        return redirect()->route('donor.payment.confirmation', $id);
    }

    public function paymentConfirmation(int $id)
    {
        $donation = $this->ownDonationsQuery()
            ->with('campaign')
            ->findOrFail($id);

        return view('donor.payment.payment-confirmation', compact('donation'));
    }

    public function history(Request $request)
    {
        $donorId = $this->donorId();

        $baseQuery = Donation::query()->where('user_id', $donorId);
        $totalDonated = (float) (clone $baseQuery)
            ->where('status', 'Allocated')
            ->sum('amount');
        $totalTransactions = (clone $baseQuery)->count();
        // Latest successful donation only; pending and failed attempts remain in transaction history.
        $latestAmount = (float) ((clone $baseQuery)
            ->where('status', 'Allocated')
            ->latest('created_at')
            ->value('amount') ?? 0);

        $historyQuery = (clone $baseQuery)->with('campaign');

        if ($request->filled('status') && in_array($request->status, ['Pending', 'Allocated', 'Failed'], true)) {
            $historyQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $historyQuery->where(function ($query) use ($search) {
                $query->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('campaign_type', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
                        $campaignQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $history = $historyQuery->latest()->paginate(10)->withQueryString();
        $status = $request->input('status');
        $search = $request->input('search');

        return view('donor.history.index', compact(
            'history',
            'status',
            'search',
            'totalDonated',
            'totalTransactions',
            'latestAmount'
        ));
    }

    public function historyDetails(int $id)
    {
        return $this->paymentConfirmation($id);
    }

    public function downloadReceipt(int $id)
    {
        $donation = $this->ownDonationsQuery()
            ->with('campaign')
            ->where('status', 'Allocated')
            ->findOrFail($id);

        $data = [
            'donorName' => $donation->contributor,
            'campaign' => $donation->campaign?->name ?? ($donation->campaign_id ? ($donation->campaign_type ?: 'Campaign Fund') : 'General Fund'),
            'category' => $donation->campaign_type,
            'amount' => $donation->amount,
            'method' => $donation->payment_method,
            'datetime' => $donation->created_at->timezone('Asia/Kuala_Lumpur'),
            'status' => $donation->status,
            'frequency' => $donation->frequency ?? 'one_time',
            'receiptEmail' => $donation->receipt_email,
            'dedication' => $donation->dedication,
        ];

        $pdf = Pdf::loadView('donor.receipts.pdf', $data);

        return $pdf->download('SelangorCareConnect-Donation-Receipt.pdf');
    }

    public function notifications()
    {
        $donorId = $this->donorId();

        $notifications = UserNotification::query()
            ->with('notification')
            ->where('user_id', $donorId)
            ->whereHas('notification')
            ->latest()
            ->get();

        return view('donor.notifications', compact('notifications'));
    }

    public function markNotificationRead(int $id)
    {
        $donorId = $this->donorId();

        $receipt = UserNotification::query()
            ->where('id', $id)
            ->where('user_id', $donorId)
            ->firstOrFail();

        if (!$receipt->is_read) {
            $receipt->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
}
