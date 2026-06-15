<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\VolunteerApplication;
use App\Models\Attendance;

class VolunteerPortalController extends Controller
{
    public function application($id)
    {
        $campaigns = [

            [
                'id' => 1,
                'title' => 'Sungai Klang Rehabilitation',
                'category' => 'Environment',
                'location' => 'Gombak',
                'date' => '22 Oct 2026',

                'shifts' => [

                    [
                        'name' => 'Morning Shift (Slot A)',
                        'time' => '08:00 AM - 12:00 PM',
                        'remaining' => 5
                    ],

                    [
                        'name' => 'Afternoon Shift (Slot B)',
                        'time' => '01:00 PM - 05:00 PM',
                        'remaining' => 2
                    ],

                    [
                        'name' => 'Full Day (Slot C)',
                        'time' => '08:00 AM - 05:00 PM',
                        'remaining' => 10
                    ]

                ],

                'skills' => [

                    'General Volunteer',
                    'First Aid',
                    'Environmental Cleanup',
                    'Logistics Handling'

                ]
            ]

        ];

        $campaign = collect($campaigns)
            ->firstWhere('id', (int) $id);

        if (!$campaign) {
            abort(404);
        }

        return view(
            'volunteer.application',
            compact('campaign')
        );
    }

    public function apply(Request $request)
    {
        $applications =
            session(
                'volunteer_applications',
                []
            );

        $applications[] = [

            'id' =>
                'VOL-' .
                rand(1000, 9999),

            'campaign' =>
                $request->campaign,

            'shift' =>
                $request->shift,

            'skill' => $request->skill,
            'notes' =>
                $request->notes,

            'status' =>
                'Under Review',

            'date' =>
                now()->format('d M Y'),

            'submitted_at' =>
                now()->format('d M Y H:i')

        ];

        session([
            'volunteer_applications' =>
                $applications
        ]);

        return redirect()
            ->route('volunteer.application.success');
    }
    public function applications()
    {
        $applications =
            session(
                'volunteer_applications',
                []
            );

        return view(
            'volunteer.applications',
            compact(
                'applications'
            )
        );
    }

    public function applicationSuccess()
    {
        $applications = session(
            'volunteer_applications',
            []
        );

        $application = end($applications);

        return view(
            'volunteer.application-success',
            compact('application')
        );
    }

    public function withdraw($id)
    {
        $applications =
            session(
                'volunteer_applications',
                []
            );

        foreach ($applications as $index => $application) {

            if ($application['id'] == $id) {

                $applications[$index]['status'] =
                    'Withdrawn';

                break;
            }
        }

        session([
            'volunteer_applications' =>
                $applications
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Your volunteer application has been withdrawn successfully.'
            );
    }
    public function dashboard()
    {
        $userId = session('user.id');

        $applications =
            VolunteerApplication::with('campaign')
                ->where(
                    'user_id',
                    $userId
                )
                ->latest()
                ->get();

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

        $hours =
            Attendance::where(
                'user_id',
                $userId
            )
                ->sum('hours_served');

        $campaigns =
            Campaign::where(
                'status',
                'Approved'
            )
                ->latest()
                ->take(6)
                ->get();

        return view(
            'volunteer.dashboard',
            compact(
                'applications',
                'approved',
                'pending',
                'hours',
                'campaigns'
            )
        );
    }

    public function history()
    {
        $applications =
            session(
                'volunteer_applications',
                []
            );

        return view(
            'volunteer.history',
            compact('applications')
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