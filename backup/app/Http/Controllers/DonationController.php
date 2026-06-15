<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Donation;
use App\Models\Campaign;

class DonationController extends Controller
{
    // DISPLAY DONATION LIST
    public function index(Request $request)
    {
        $donations = Donation::with(
            'campaign',
            'user'
        )
            ->latest()
            ->get();

        if ($request->search) {

            $search = strtolower($request->search);

            $donations = $donations->filter(
                function ($d) use ($search) {

                    return str_contains(
                        strtolower($d->contributor ?? ''),
                        $search
                    )

                        ||

                        str_contains(
                            strtolower($d->transaction_id ?? ''),
                            $search
                        )

                        ||

                        str_contains(
                            strtolower($d->campaign->name ?? ''),
                            $search
                        );

                }
            );
        }

        if ($request->status) {

            $donations = $donations->where(
                'status',
                $request->status
            );
        }

        $totalCollections =
            Donation::where(
                'status',
                'Allocated'
            )->sum('amount');

        $totalAllocated =
            Donation::where(
                'status',
                'Allocated'
            )->sum('amount');

        $pendingVerifications =
            Donation::where(
                'status',
                'Pending'
            )->count();

        $contributors =
            Donation::whereNotNull(
                'user_id'
            )
                ->distinct()
                ->count('user_id');

        $allocationGroups =
            $donations->groupBy(
                'campaign_type'
            );
        $monthlyCollections = Donation::selectRaw(
            'MONTH(created_at) as month,
     SUM(amount) as amount'
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view(
            'organizer.donation.index',
            compact(
                'donations',
                'totalCollections',
                'totalAllocated',
                'pendingVerifications',
                'contributors',
                'allocationGroups',
                'monthlyCollections'
            )
        );
    }


    // SHOW CREATE PAGE
    public function create()
    {
        $campaigns = Campaign::where(
            'status',
            'Approved'
        )->get();

        return view(
            'organizer.donation.create',
            compact('campaigns')
        );
    }
    // STORE NEW DONATION
    public function store(Request $request)
    {
        Donation::create([

            'contributor' => $request->contributor,

            'campaign_id' => $request->campaign_id,

            'amount' => str_replace(',', '', $request->amount),

            'status' => $request->status,

            'transaction_id' => 'TXN-' . rand(10000000, 99999999),

            'payment_method' => 'Manual',

            'receipt_no' => 'RCP-' . rand(10000, 99999)

        ]);

        return redirect('/donation')
            ->with('success', 'Donation recorded successfully.');
    }
    // SHOW DONATION DETAILS - VIEW PAGE
    public function show($id)
    {
        $donation = Donation::with(
            'user',
            'campaign'
        )->findOrFail($id);

        return view(
            'organizer.donation.view',
            compact('donation')
        );
    }

    // SHOW EDIT PAGE
    public function edit($id)
    {
        $donations = collect(session('donations', []));

        $defaultDonations = collect([

            [
                'id' => 1,
                'date' => '24 Oct 2023, 14:30',
                'contributor' => 'Ahmad Roslan bin Said',
                'transaction_id' => 'TXN-99827361',
                'campaign' => 'Selangor Flood Relief 2023',
                'campaign_type' => 'Disaster Relief',
                'amount' => '5000.00',
                'status' => 'Allocated',
                'color' => 'red'
            ],

            [
                'id' => 2,
                'date' => '24 Oct 2023, 13:15',
                'contributor' => 'Siti Noorhaliza',
                'transaction_id' => 'TXN-99827355',
                'campaign' => 'Education for All: B40 Initiative',
                'campaign_type' => 'Education',
                'amount' => '250.00',
                'status' => 'Pending',
                'color' => 'yellow'
            ],

            [
                'id' => 3,
                'date' => '23 Oct 2023, 16:45',
                'contributor' => 'LKL Corporate Sdn Bhd',
                'transaction_id' => 'TXN-99827340',
                'campaign' => 'Digital Literacy Program',
                'campaign_type' => 'Medical Support',

                'amount' => '25000.00',
                'status' => 'Allocated',
                'color' => 'blue'
            ],

        ]);

        $donations = $defaultDonations->merge($donations);

        $donation = collect($donations)->firstWhere('id', $id);

        return view('organizer.donation.edit', compact('donation'));
    }

    // UPDATE DONATION
    public function update(Request $request, $id)
    {
        $donations = session('donations', []);

        foreach ($donations as &$donation) {

            if ($donation['id'] == $id) {

                $donation['contributor'] = $request->contributor;

                $donation['campaign'] = $request->campaign;
                $donation['campaign_type'] = $request->campaign_type;

                $donation['amount'] = str_replace(',', '', $request->amount);

                $donation['status'] = $request->status;

                $donation['color'] = $request->status == 'Allocated'
                    ? 'red'
                    : 'yellow';
            }
        }

        session(['donations' => $donations]);

        return redirect('/donation')
            ->with('success', 'Donation record updated successfully.');
    }

    // DELETE DONATION
    public function delete($id)
    {
        $donations = session('donations', []);

        $donations = collect($donations)
            ->reject(function ($donation) use ($id) {

                return $donation['id'] == $id;

            })
            ->values()
            ->toArray();

        session(['donations' => $donations]);

        return redirect('/donation')
            ->with('success', 'Donation record deleted successfully.');
    }

    public function allocationDetails()
    {
        $defaultDonations = [

            [
                'campaign' => 'Selangor Flood Relief 2023',
                'campaign_type' => 'Disaster Relief',
                'contributor' => 'Ahmad Roslan bin Said',
                'amount' => '5000.00',
            ],

            [
                'campaign' => 'Education for All: B40 Initiative',
                'campaign_type' => 'Education',
                'contributor' => 'Siti Noorhaliza',
                'amount' => '250.00',
            ],

        ];

        $sessionDonations = session('donations', []);

        $donations = collect(array_merge(
            $defaultDonations,
            $sessionDonations
        ));

        $allocationGroups = $donations->groupBy('campaign_type');

        return view(
            'organizer.donation.allocation-details',
            compact('allocationGroups')
        );
    }
    public function exportPdf()
    {
        $donations = Donation::all();

        $pdf = PDF::loadView(
            'organizer.donation.report',
            compact('donations')
        );

        return $pdf->download(
            'Donation_Report.pdf'
        );
    }
}