<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    private function getMockData()
    {
        return [

            [
                'id' => 1,
                'name' => 'Nurul Izzah',
                'email' => 'n.izzah@email.com',
                'skills' => ['Education', 'First Aid'],
                'status' => 'Active',
                'registered_date' => '2026-05-01'
            ],

            [
                'id' => 2,
                'name' => 'Tan Ah Kiat',
                'email' => 'tan.ak@jaring.my',
                'skills' => ['Finance', 'Mentoring'],
                'status' => 'Pending',
                'registered_date' => '2026-05-15'
            ],

            [
                'id' => 3,
                'name' => 'Ramesh Kumar',
                'email' => 'ramesh.k@outlook.com',
                'skills' => ['Logistics', 'Language'],
                'status' => 'Inactive',
                'registered_date' => '2026-06-02'
            ],

        ];
    }

    public function index(Request $request)
    {
        $volunteers = collect($this->getMockData());

        // SEARCH FILTER
        if ($request->search) {

            $search = strtolower($request->search);

            $volunteers = $volunteers->filter(function ($v) use ($search) {

                return str_contains(strtolower($v['name']), $search)
                    || str_contains(strtolower($v['email']), $search)
                    || collect($v['skills'])->contains(function ($skill) use ($search) {

                        return str_contains(strtolower($skill), $search);

                    });

            });

        }

        // DATE FILTER
        if ($request->date) {

            $volunteers = $volunteers->filter(function ($v) use ($request) {

                return $v['registered_date'] == $request->date;

            });

        }

        // STATUS FILTER
        if ($request->status) {

            $volunteers = $volunteers->filter(function ($v) use ($request) {

                return $v['status'] == $request->status;

            });

        }

        // DASHBOARD COUNTS
        $totalVolunteers = count($this->getMockData());

        $activeThisMonth = collect($this->getMockData())
            ->where('status', 'Active')
            ->count();

        $pendingApprovals = collect($this->getMockData())
            ->where('status', 'Pending')
            ->count();

        $inactiveVolunteers = collect($this->getMockData())
            ->where('status', 'Inactive')
            ->count();

        return view('organizer.volunteers.index', [

            'volunteers' => $volunteers,
            'totalVolunteers' => $totalVolunteers,
            'activeThisMonth' => $activeThisMonth,
            'pendingApprovals' => $pendingApprovals,
            'inactiveVolunteers' => $inactiveVolunteers,

        ]);

    }

    public function create()
    {
        return view('organizer.volunteers.create');
    }

    public function store(Request $request)
    {
        return redirect('/volunteers')
            ->with('success', 'Volunteer added (mock)');
    }

    public function edit($id)
    {
        $volunteer = collect($this->getMockData())
            ->firstWhere('id', $id);

        return view('organizer.volunteers.edit', compact('volunteer'));
    }

    public function update(Request $request, $id)
    {
        return redirect('/volunteers')
            ->with('success', 'Volunteer updated (mock)');
    }

    public function delete($id)
    {
        return redirect('/volunteers')
            ->with('success', 'Volunteer deleted (mock)');
    }
}