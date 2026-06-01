<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DonationController extends Controller
{
    // DISPLAY DONATION LIST
    public function index(Request $request)
    {
        // DEFAULT DUMMY DATA
        $defaultDonations = [

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

        ];

        // NEWLY ADDED DONATIONS
        $sessionDonations = session('donations', []);

        // MERGE BOTH
        $donations = collect(array_merge($defaultDonations, $sessionDonations));

        // SEARCH FILTER
        if ($request->search) {

            $search = strtolower($request->search);

            $donations = $donations->filter(function ($d) use ($search) {

                return str_contains(strtolower($d['contributor']), $search)
                    || str_contains(strtolower($d['campaign']), $search)
                    || str_contains(strtolower($d['transaction_id']), $search);

            });
        }

        // STATUS FILTER
        if ($request->status) {

            $donations = $donations->where('status', $request->status);

        }
        // DYNAMIC LAST 7 MONTHS COLLECTION DATA
        $monthlyCollections = [];

        for ($i = 6; $i >= 0; $i--) {

            $monthlyCollections[] = [

                // AUTO MONTH + YEAR
                'month' => now()
                    ->subMonths($i)
                    ->format('M Y'),

                // TEMP MOCK AMOUNT
                // Replace with DB total later
                'amount' => rand(5000, 50000),

            ];
        }
        // SUMMARY CARD CALCULATIONS

        $totalCollections = $donations->sum(function ($d) {

            return (float) $d['amount'];

        });

        $totalAllocated = $donations
            ->where('status', 'Allocated')
            ->sum(function ($d) {

                return (float) $d['amount'];

            });

        $pendingVerifications = $donations
            ->where('status', 'Pending')
            ->count();

        $contributors = $donations->count();

        // GROUP DONATIONS BY CAMPAIGN TYPE
        $allocationGroups = $donations->groupBy('campaign_type');
        return view('organizer.donation.index', [

            'donations' => $donations,

            'monthlyCollections' => $monthlyCollections,

            'totalCollections' => number_format($totalCollections, 2),

            'totalAllocated' => number_format($totalAllocated, 2),

            'pendingVerifications' => $pendingVerifications,

            'contributors' => $contributors,
            'allocationGroups' => $allocationGroups,

        ]);

    }

    // SHOW CREATE PAGE
    public function create()
    {
        return view('organizer.donation.create');
    }

    // STORE NEW DONATION
    public function store(Request $request)
    {
        $donations = session('donations', []);

        $donations[] = [

            'id' => count($donations) + 1,

            'date' => now()->format('d M Y, H:i'),

            'contributor' => $request->contributor,

            'transaction_id' => 'TXN-' . rand(10000000, 99999999),

            'campaign' => $request->campaign,
            'campaign_type' => $request->campaign_type,

            'amount' => str_replace(',', '', $request->amount),

            'status' => $request->status,

            'color' => $request->status == 'Allocated'
                ? 'red'
                : 'yellow',
        ];

        session(['donations' => $donations]);

        return redirect('/donation')
            ->with('success', 'Donation recorded successfully.');
    }

    // SHOW DONATION DETAILS - VIEW PAGE
    public function show($id)
    {
        $defaultDonations = [

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
                'campaign_type' => 'Disaster Relief',
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
                'campaign_type' => 'Disaster Relief',
                'amount' => '25000.00',
                'status' => 'Allocated',
                'color' => 'blue'
            ],

        ];

        $sessionDonations = session('donations', []);

        $donations = collect(array_merge($defaultDonations, $sessionDonations));

        $donation = $donations->firstWhere('id', $id);

        return view('organizer.donation.view', compact('donation'));
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
}