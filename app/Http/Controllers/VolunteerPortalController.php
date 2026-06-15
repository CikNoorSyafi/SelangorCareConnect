<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\VolunteerApplication;
use App\Models\Attendance;
use App\Models\CampaignSkill;
use Illuminate\Support\Facades\Auth;


class VolunteerPortalController extends Controller
{
    public function application($id)
    {
        $campaign = Campaign::findOrFail($id);

        $skills = CampaignSkill::where(
            'campaign_id',
            $id
        )
            ->with('skill')
            ->get();

        $shifts = \App\Models\Shift::where(
            'status',
            'Active'
        )->get();

        $existingApplication =
            VolunteerApplication::where(
                'campaign_id',
                $id
            )
                ->where(
                    'user_id',
                    session('user.id')
                )
                ->first();

        if ($existingApplication) {

            return redirect()
                ->route('volunteer.applications')
                ->with(
                    'error',
                    'You have already applied for this campaign.'
                );
        }

        return view(
            'volunteer.application',
            compact(
                'campaign',
                'skills',
                'shifts'
            )
        );
    }

    public function apply(Request $request)
    {
        $request->validate([

            'campaign_id' => 'required',
            'shift' => 'required',
            'skill' => 'required',

        ]);
        $existingApplication =
            VolunteerApplication::where(
                'user_id',
                session('user.id')
            )
                ->where(
                    'campaign_id',
                    $request->campaign_id
                )
                ->whereNotIn(
                    'status',
                    ['Withdrawn']
                )
                ->first();

        if ($existingApplication) {

            return back()
                ->with(
                    'error',
                    'You have already applied for this campaign.'
                );
        }

        VolunteerApplication::create([

            'user_id' => session('user.id'),

            'campaign_id' => $request->campaign_id,

            'shift' => $request->shift,

            'skill' => $request->skill,

            'notes' => $request->notes,

            'status' => 'Pending'

        ]);

        return redirect()
            ->route('volunteer.application.success');
    }
    public function applications()
    {
        $applications =
            VolunteerApplication::with('campaign')
                ->where(
                    'user_id',
                    session('user.id')
                )
                ->latest()
                ->get();

        return view(
            'volunteer.applications',
            compact('applications')
        );
    }

    public function applicationSuccess()
    {
        $application =
            VolunteerApplication::with('campaign')
                ->where(
                    'user_id',
                    session('user.id')
                )
                ->latest()
                ->first();

        return view(
            'volunteer.application-success',
            compact('application')
        );
    }
    public function withdraw($id)
    {
        $application =
            VolunteerApplication::where(
                'user_id',
                session('user.id')
            )
                ->findOrFail($id);

        $application->update([
            'status' => 'Withdrawn'
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Application withdrawn successfully.'
            );
    }
    public function dashboard()
    {

        $userId = session('user.id');

        $totalApplications =
            VolunteerApplication::where(
                'user_id',
                $userId
            )->count();

        $approved =
            VolunteerApplication::where(
                'user_id',
                $userId
            )
                ->where(
                    'status',
                    'Approved'
                )
                ->count();

        $pending =
            VolunteerApplication::where(
                'user_id',
                $userId
            )
                ->where(
                    'status',
                    'Pending'
                )
                ->count();

        $hours = 0;

        $activeApplications =
            VolunteerApplication::with(
                'campaign'
            )
                ->where(
                    'user_id',
                    $userId
                )
                ->whereIn(
                    'status',
                    ['Pending', 'Approved']
                )
                ->get();

        $appliedCampaignIds =
            VolunteerApplication::where(
                'user_id',
                $userId
            )
                ->pluck('campaign_id');

        $campaigns =
            Campaign::where(
                'status',
                'Approved'
            )
                ->whereNotIn(
                    'id',
                    $appliedCampaignIds
                )
                ->latest()
                ->get();

        return view(
            'volunteer.dashboard',
            compact(
                'totalApplications',
                'approved',
                'pending',
                'hours',
                'activeApplications',
                'campaigns'
            )
        );
    }


    public function history()
    {
        $applications =
            VolunteerApplication::with('campaign')
                ->where(
                    'user_id',
                    session('user.id')
                )
                ->where(
                    'status',
                    'Approved'
                )
                ->latest()
                ->get();

        return view(
            'volunteer.history',
            compact('applications')
        );
    }
    public function viewApplication($id)
    {
        $application =
            VolunteerApplication::with('campaign')
                ->where(
                    'user_id',
                    session('user.id')
                )
                ->findOrFail($id);

        return view(
            'volunteer.application-view',
            compact('application')
        );
    }
    public function assignments()
    {
        $assignments =
            VolunteerApplication::with('campaign')
                ->where(
                    'user_id',
                    session('user.id')
                )
                ->where(
                    'status',
                    'Approved'
                )
                ->latest()
                ->get();

        return view(
            'volunteer.assignments',
            compact('assignments')
        );
    }
    public function attendance()
    {
        $attendance = [

            [
                'campaign' => 'Flood Relief Support',
                'date' => '12 Jun 2026',
                'checkin' => '08:00 AM',
                'checkout' => '12:00 PM',
                'hours' => 4
            ],

            [
                'campaign' => 'Food Aid Distribution',
                'date' => '18 Jun 2026',
                'checkin' => '09:00 AM',
                'checkout' => '05:00 PM',
                'hours' => 8
            ]

        ];

        return view(
            'volunteer.attendance',
            compact('attendance')
        );
    }

    public function profile()
    {
        $user = session('user', [

            'name' => 'Ahmad Z.',
            'email' => 'volunteer@test.com',
            'role' => 'volunteer'

        ]);

        return view(
            'volunteer.profile',
            compact('user')
        );
    }
}