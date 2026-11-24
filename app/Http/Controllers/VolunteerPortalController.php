<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\VolunteerApplication;
use App\Models\Attendance;
use App\Models\CampaignSkill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\CampaignVolunteer;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\ActivityLog;



class VolunteerPortalController extends Controller
{
    public function application($id)
    {
        $campaign = Campaign::where('status', 'Recruiting')->findOrFail($id);

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
                ->whereIn('status', ['Pending', 'Approved', 'Rejected'])
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
        $validated = $request->validate([
            'campaign_id' => ['required', 'integer'],
            'shift' => ['required', 'string', 'max:255', Rule::exists('shifts', 'name')->where('status', 'Active')],
            'skill' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $volunteer = \App\Models\User::where('role', 'volunteer')
            ->where('volunteer_status', 'Active')
            ->findOrFail(session('user.id'));

        $campaign = Campaign::where('status', 'Recruiting')
            ->findOrFail($validated['campaign_id']);

        $existingApplication = VolunteerApplication::where('user_id', $volunteer->id)
            ->where('campaign_id', $campaign->id)
            ->whereIn('status', ['Pending', 'Approved', 'Rejected'])
            ->exists();

        if ($existingApplication) {
            return back()->with('error', 'You already have an application for this campaign.');
        }

        VolunteerApplication::create([
            'user_id' => $volunteer->id,
            'campaign_id' => $campaign->id,
            'shift' => $validated['shift'],
            'skill' => $validated['skill'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'Pending',
        ]);

        $this->notifyOrganizer($campaign, 'New Volunteer Application', $volunteer->name . ' submitted an application for ' . $campaign->name . '.');
        ActivityLog::record('volunteer.application_submitted', "{$volunteer->name} applied for campaign '{$campaign->name}'.");

        return redirect()->route('volunteer.application.success');
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
        $application = VolunteerApplication::with('campaign')
            ->where('user_id', session('user.id'))
            ->findOrFail($id);

        if ($application->status == 'Pending') {

            $application->update([
                'status' => 'Withdrawn'
            ]);

            $message =
                'Application withdrawn successfully.';
        } elseif ($application->status == 'Approved') {

            if (Attendance::where('campaign_id', $application->campaign_id)
                ->where('user_id', $application->user_id)
                ->exists()) {
                return back()->with(
                    'error',
                    'This participation has attendance history and can no longer be withdrawn. Please contact the organizer.'
                );
            }

            $application->update([
                'status' => 'Withdrawn'
            ]);

            CampaignVolunteer::where(
                'campaign_id',
                $application->campaign_id
            )
                ->where(
                    'volunteer_id',
                    session('user.id')
                )
                ->delete();

            $message =
                'Campaign participation withdrawn successfully.';
        } else {

            return back()->with(
                'error',
                'This application cannot be withdrawn.'
            );
        }

        $this->notifyOrganizer($application->campaign, 'Volunteer Application Withdrawn', 'A volunteer has withdrawn their application/participation for ' . $application->campaign->name . '.');
        ActivityLog::record('volunteer.application_withdrawn', "Volunteer '{$application->user->name}' withdrew their application for campaign '{$application->campaign->name}'.");

        return back()->with('success', $message);
    }

    private function notifyOrganizer(Campaign $campaign, string $title, string $message): void
    {
        $organizerId = (int) ($campaign->organizer_id ?? 0);
        if ($organizerId <= 0) {
            return;
        }

        $notification = Notification::create([
            'organizer_id' => $organizerId,
            'title' => $title,
            'message' => $message,
            'type' => 'System',
            'audience' => 'Organizer',
            'campaign_id' => $campaign->id,
            'status' => 'Sent',
            'recipients' => 1,
        ]);

        UserNotification::create([
            'user_id' => $organizerId,
            'notification_id' => $notification->id,
            'is_read' => false,
        ]);
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

        $hours = Attendance::where('user_id', $userId)
            ->where('status', 'Present')
            ->sum('hours_served');

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
                ->whereIn(
                    'status',
                    [
                        'Pending',
                        'Approved',
                        'Rejected'
                    ]
                )
                ->pluck('campaign_id');

        $campaigns =
            Campaign::where('status', 'Recruiting')
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

    public function assignments()
    {
        $assignments =
            CampaignVolunteer::with([
                'campaign',
                'role',
                'shift'
            ])
                ->where(
                    'volunteer_id',
                    session('user.id')
                )
                ->latest()
                ->get();

        return view(
            'volunteer.assignments',
            compact('assignments')
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
                ->whereIn(
                    'status',
                    [
                        'Approved',
                        'Withdrawn'
                    ]
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

    public function attendance()
    {
        $userId = session('user.id');

        $attendanceQuery = Attendance::with('campaign')
            ->where('user_id', $userId);

        $totalHours = (int) (clone $attendanceQuery)
            ->where('status', 'Present')
            ->sum('hours_served');

        $campaignsAttended = (clone $attendanceQuery)
            ->where('status', 'Present')
            ->distinct('campaign_id')
            ->count('campaign_id');

        $recordedSessions = (clone $attendanceQuery)->count();
        $presentSessions = (clone $attendanceQuery)
            ->where('status', 'Present')
            ->count();

        $attendanceRate = $recordedSessions > 0
            ? round(($presentSessions / $recordedSessions) * 100, 1)
            : 0;

        $attendance = $attendanceQuery
            ->latest('attendance_date')
            ->paginate(10);

        return view('volunteer.attendance', compact(
            'attendance', 'totalHours', 'campaignsAttended', 'attendanceRate'
        ));
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