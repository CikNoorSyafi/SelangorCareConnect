<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Campaign;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Skill;
use App\Models\VolunteerRole;
use App\Models\Shift;
use App\Models\CampaignVolunteer;
use App\Models\CampaignType;
use App\Models\CampaignSkill;
use App\Models\VolunteerApplication;
use App\Models\Attendance;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Support\SelangorDistricts;
use Illuminate\Validation\Rule;


class CampaignController extends Controller
{
    public function index()
    {
        $volunteers = $this->eligibleVolunteersQuery()->get();

        $skills = Skill::where(
            'status',
            'Active'
        )->get();

        $roles = VolunteerRole::where(
            'status',
            'Active'
        )->get();

        $shifts = Shift::where(
            'status',
            'Active'
        )->get();

        $campaignTypes = CampaignType::where(
            'status',
            'Active'
        )->get();


        $search = request('search');
        $mapCampaignId = request()->query('map_campaign_id');
        $selectedMapCampaignId = (is_scalar($mapCampaignId) && ctype_digit((string) $mapCampaignId))
            ? (int) $mapCampaignId
            : null;
        $districtParam = request()->query('district');
        $districtFilter = is_string($districtParam) ? SelangorDistricts::resolve($districtParam) : null;

        $organizerId = (int) session('user.id');

        $ownedCampaigns = Campaign::query()
            ->where('organizer_id', $organizerId);

        // A map-pin selection takes priority so the selected campaign is always
        // brought into the Existing Campaigns table, even when it is on another page.
        if ($selectedMapCampaignId !== null) {
            $campaigns = (clone $ownedCampaigns)
                ->withCount('volunteers')
                ->whereKey($selectedMapCampaignId);
        } else {
            $campaigns = (clone $ownedCampaigns)
                ->withCount('volunteers')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($searchQuery) use ($search) {
                        $searchQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%");
                    });
                });

            if ($districtFilter) {
                $campaigns->whereIn('location', SelangorDistricts::databaseValuesFor($districtFilter));
            }

            if (request('status') == 'active') {
                $campaigns->whereIn('status', ['Recruiting', 'In Progress']);
            }

            if (request('status') == 'fundraising') {
                $campaigns->where('status', 'Fundraising');
            }

            if (request('status') == 'completed') {
                $campaigns->where('status', 'Completed');
            }
        }

        $campaigns = $campaigns
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $campaignMapPoints = (clone $ownedCampaigns)
            ->latest()
            ->get(['id', 'name', 'location', 'status', 'start_date', 'end_date', 'description'])
            ->map(fn ($campaign) => [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'location' => $campaign->location,
                'district' => SelangorDistricts::resolve($campaign->location),
                'status' => $campaign->status,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'description' => $campaign->description,
            ])->values();

        $totalCampaigns = (clone $ownedCampaigns)->count();

        $activeCampaigns = (clone $ownedCampaigns)->whereIn(
            'status',
            ['Recruiting', 'In Progress']
        )->count();

        $pendingCampaigns = (clone $ownedCampaigns)->where(
            'status',
            'Fundraising'
        )->count();

        $completedCampaigns = (clone $ownedCampaigns)->where(
            'status',
            'Completed'
        )->count();


        $districts = SelangorDistricts::all();

        return view(
            'organizer.campaign',
            compact(
                'volunteers',
                'skills',
                'roles',
                'shifts',
                'campaignTypes',
                'campaigns',
                'totalCampaigns',
                'activeCampaigns',
                'pendingCampaigns',
                'completedCampaigns',
                'districts',
                'campaignMapPoints',
                'selectedMapCampaignId',
                'districtFilter'
            )
        );
    }
    public function create()
    {
        $skills = Skill::where('status', 'Active')->get();

        $roles = VolunteerRole::where('status', 'Active')->get();

        $shifts = Shift::where('status', 'Active')->get();

        $volunteers = $this->eligibleVolunteersQuery()->get();

        $campaignTypes = CampaignType::where('status', 'Active')->get();

        $districts = SelangorDistricts::all();

        return view(
            'organizer.create-campaign',
            compact(
                'skills',
                'roles',
                'shifts',
                'volunteers',
                'campaignTypes',
                'districts'
            )
        );
    }
    public function store(Request $request)
    {
        $request->merge([
            'funding_target' => str_replace(',', '', (string) $request->input('funding_target')),
        ]);

        $validated = $request->validate([
            'campaign_name' => ['required', 'string', 'max:255'],
            'campaign_type' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', Rule::in(SelangorDistricts::all())],
            'funding_target' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', Rule::exists('skills', 'id')->where('status', 'Active')],
            'volunteers' => ['nullable', 'array'],
            'volunteers.*' => ['integer', Rule::exists('users', 'id')->where('role', 'volunteer')],
            'role_id' => ['nullable', 'array'],
            'role_id.*' => ['required', 'integer', Rule::exists('volunteer_roles', 'id')->where('status', 'Active')],
            'shift_id' => ['nullable', 'array'],
            'shift_id.*' => ['required', 'integer', Rule::exists('shifts', 'id')->where('status', 'Active')],
        ]);

        $organizerId = (int) session('user.id');
        $volunteerIds = collect($validated['volunteers'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $roleIds = $validated['role_id'] ?? [];
        $shiftIds = $validated['shift_id'] ?? [];

        foreach ($volunteerIds as $volunteerId) {
            if (empty($roleIds[$volunteerId]) || empty($shiftIds[$volunteerId])) {
                return back()->withErrors(['volunteers' => 'Please select an active role and shift for every selected volunteer.'])->withInput();
            }

            $volunteer = User::query()
                ->where('role', 'volunteer')
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('volunteer_status')->orWhere('volunteer_status', 'Active');
                })
                ->find($volunteerId);

            if (!$volunteer) {
                return back()->withErrors(['volunteers' => 'One or more selected volunteer accounts are inactive.'])->withInput();
            }

            $committedElsewhere = CampaignVolunteer::query()
                ->where('volunteer_id', $volunteerId)
                ->whereHas('campaign', function ($query) use ($organizerId) {
                    $query->where('organizer_id', '!=', $organizerId)
                        ->whereIn('status', ['Fundraising', 'Recruiting', 'In Progress']);
                })
                ->exists();

            if ($committedElsewhere) {
                return back()->withErrors(['volunteers' => $volunteer->name . ' is already assigned to another organizer’s active campaign.'])->withInput();
            }
        }

        DB::transaction(function () use ($validated, $volunteerIds, $roleIds, $shiftIds, $organizerId) {
            $campaign = Campaign::create([
                'organizer_id' => $organizerId,
                'name' => $validated['campaign_name'],
                'type' => $validated['campaign_type'],
                'location' => $validated['location'],
                'target' => (float) $validated['funding_target'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'] ?? null,
                'status' => 'Fundraising',
            ]);

            foreach (array_unique($validated['skills'] ?? []) as $skillId) {
                CampaignSkill::create(['campaign_id' => $campaign->id, 'skill_id' => $skillId]);
            }

            foreach ($volunteerIds as $volunteerId) {
                CampaignVolunteer::create([
                    'campaign_id' => $campaign->id,
                    'volunteer_id' => $volunteerId,
                    'role_id' => $roleIds[$volunteerId],
                    'shift_id' => $shiftIds[$volunteerId],
                ]);
            }

            if ($volunteerIds->isNotEmpty()) {
                $notification = Notification::create([
                    'organizer_id' => $organizerId,
                    'title' => 'New Campaign Assignment',
                    'message' => 'You have been assigned to ' . $campaign->name . '. Please review your assignment details in the Volunteer Portal.',
                    'type' => 'System',
                    'audience' => 'Campaign Volunteers',
                    'campaign_id' => $campaign->id,
                    'status' => 'Sent',
                    'recipients' => $volunteerIds->count(),
                ]);
                $now = now();
                UserNotification::insert($volunteerIds->map(fn ($volunteerId) => [
                    'user_id' => $volunteerId,
                    'notification_id' => $notification->id,
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());
            }
        });

        ActivityLog::record('campaign.created', "Created campaign '{$validated['campaign_name']}' and assigned {$volunteerIds->count()} volunteer(s).");

        return redirect('/campaign')->with('success', 'Campaign created successfully.');
    }

    public function delete($id)
    {
        $campaign = $this->findOwnedCampaign($id);

        $hasFinancialHistory = $campaign->donations()->exists();
        $hasAttendanceHistory = Attendance::where('campaign_id', $campaign->id)->exists();
        $hasVolunteerHistory = $campaign->volunteers()->exists() || $campaign->applications()->exists();
        $hasNotificationHistory = Notification::where('campaign_id', $campaign->id)->exists();
        if ($hasFinancialHistory || $hasAttendanceHistory || $hasVolunteerHistory || $hasNotificationHistory) {
            return back()->with('error', 'This campaign has donation, volunteer, attendance or notification history and cannot be deleted. Mark it Completed to preserve its records.');
        }

        $campaignName = $campaign->name;
        $campaign->delete();
        ActivityLog::record('campaign.deleted', "Deleted campaign '{$campaignName}'.");

        return redirect('/campaign')->with('success', 'Campaign deleted successfully.');
    }

    public function edit($id)
    {
        $campaign = $this->findOwnedCampaign($id);

        $skills = Skill::where(
            'status',
            'Active'
        )->get();

        $selectedSkillIds =
            CampaignSkill::where(
                'campaign_id',
                $campaign->id
            )
                ->pluck('skill_id')
                ->toArray();

        $volunteers = $this->eligibleVolunteersQuery($campaign->id)->get();

        $assignedVolunteerIds =
            CampaignVolunteer::where(
                'campaign_id',
                $campaign->id
            )
                ->pluck('volunteer_id')
                ->toArray();
        $assignedAssignments =
            CampaignVolunteer::where(
                'campaign_id',
                $campaign->id
            )
                ->get()
                ->keyBy('volunteer_id');
        $roles = VolunteerRole::where(
            'status',
            'Active'
        )->get();

        $shifts = Shift::where(
            'status',
            'Active'
        )->get();

        $campaignTypes = CampaignType::where(
            'status',
            'Active'
        )->get();

        $districts = SelangorDistricts::all();
        $selectedDistrict = SelangorDistricts::resolve($campaign->location);

        return view(
            'organizer.edit-campaign',
            compact(
                'campaign',
                'volunteers',
                'assignedVolunteerIds',
                'roles',
                'shifts',
                'skills',
                'selectedSkillIds',
                'campaignTypes',
                'assignedAssignments',
                'districts',
                'selectedDistrict'
            )
        );
    }
    public function startRecruitment($id)
    {
        $campaign = $this->findOwnedCampaign($id);
        if ($campaign->status !== 'Fundraising') {
            return back()->with('error', 'Only fundraising campaigns can move to Recruiting.');
        }
        $campaign->update(['status' => 'Recruiting']);
        ActivityLog::record('campaign.status_changed', "Campaign '{$campaign->name}' moved from Fundraising to Recruiting.");

        return back()->with('success', 'Volunteer recruitment has started.');
    }

    public function startCampaign($id)
    {
        $campaign = $this->findOwnedCampaign($id);
        if ($campaign->status !== 'Recruiting') {
            return back()->with('error', 'Only recruiting campaigns can move to In Progress.');
        }
        $campaign->update(['status' => 'In Progress']);
        ActivityLog::record('campaign.status_changed', "Campaign '{$campaign->name}' moved from Recruiting to In Progress.");

        return back()->with('success', 'Campaign is now In Progress.');
    }

    public function completeCampaign($id)
    {
        $campaign = $this->findOwnedCampaign($id);
        if ($campaign->status !== 'In Progress') {
            return back()->with('error', 'Only In Progress campaigns can be marked Completed.');
        }
        $campaign->update(['status' => 'Completed']);
        ActivityLog::record('campaign.status_changed', "Campaign '{$campaign->name}' moved from In Progress to Completed.");

        return back()->with('success', 'Campaign marked as Completed.');
    }
    public function update(Request $request, $id)
    {
        $campaign = $this->findOwnedCampaign($id);
        $request->merge([
            'funding_target' => str_replace(',', '', (string) $request->input('funding_target')),
        ]);

        $validated = $request->validate([
            'campaign_name' => ['required', 'string', 'max:255'],
            'campaign_type' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', Rule::in(SelangorDistricts::all())],
            'funding_target' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', Rule::exists('skills', 'id')->where('status', 'Active')],
            'volunteers' => ['nullable', 'array'],
            'volunteers.*' => ['integer', Rule::exists('users', 'id')->where('role', 'volunteer')],
            'role_id' => ['nullable', 'array'],
            'role_id.*' => ['required', 'integer', Rule::exists('volunteer_roles', 'id')->where('status', 'Active')],
            'shift_id' => ['nullable', 'array'],
            'shift_id.*' => ['required', 'integer', Rule::exists('shifts', 'id')->where('status', 'Active')],
        ]);

        $organizerId = (int) session('user.id');
        $selectedIds = collect($validated['volunteers'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $roleIds = $validated['role_id'] ?? [];
        $shiftIds = $validated['shift_id'] ?? [];
        $existingAssignments = CampaignVolunteer::query()
            ->where('campaign_id', $campaign->id)
            ->get()
            ->keyBy('volunteer_id');
        $existingIds = $existingAssignments->keys()->map(fn ($id) => (int) $id);
        $removeIds = $existingIds->diff($selectedIds)->values();
        $newlyAssignedIds = $selectedIds->diff($existingIds)->values();

        // Do not silently remove a volunteer's assignment if attendance already exists.
        if ($removeIds->isNotEmpty() && Attendance::query()
            ->where('campaign_id', $campaign->id)
            ->whereIn('user_id', $removeIds)
            ->exists()) {
            return back()->withErrors([
                'volunteers' => 'A selected volunteer has recorded attendance. Remove attendance records through the approved retention process before changing this assignment.',
            ])->withInput();
        }

        foreach ($selectedIds as $volunteerId) {
            if (empty($roleIds[$volunteerId]) || empty($shiftIds[$volunteerId])) {
                return back()->withErrors(['volunteers' => 'Please select an active role and shift for every selected volunteer.'])->withInput();
            }

            // Existing assignments on this campaign are retained even if the
            // volunteer has since become inactive or gained another assignment.
            if ($existingAssignments->has($volunteerId)) {
                $existingAssignment = $existingAssignments[$volunteerId];
                $roleOrShiftChanged = (int) $existingAssignment->role_id !== (int) $roleIds[$volunteerId]
                    || (int) $existingAssignment->shift_id !== (int) $shiftIds[$volunteerId];
                if ($roleOrShiftChanged && Attendance::query()
                    ->where('campaign_id', $campaign->id)
                    ->where('user_id', $volunteerId)
                    ->exists()) {
                    return back()->withErrors(['volunteers' => 'A volunteer with attendance history cannot have their role or shift changed.'])->withInput();
                }
                continue;
            }

            $volunteer = User::query()
                ->where('role', 'volunteer')
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('volunteer_status')->orWhere('volunteer_status', 'Active');
                })
                ->find($volunteerId);

            if (!$volunteer) {
                return back()->withErrors(['volunteers' => 'One or more newly selected volunteer accounts are inactive.'])->withInput();
            }

            $committedElsewhere = CampaignVolunteer::query()
                ->where('volunteer_id', $volunteerId)
                ->whereHas('campaign', function ($query) use ($organizerId) {
                    $query->where('organizer_id', '!=', $organizerId)
                        ->whereIn('status', ['Fundraising', 'Recruiting', 'In Progress']);
                })
                ->exists();

            if ($committedElsewhere) {
                return back()->withErrors(['volunteers' => $volunteer->name . ' is already assigned to another organizer’s active campaign.'])->withInput();
            }
        }

        DB::transaction(function () use ($campaign, $validated, $selectedIds, $removeIds, $newlyAssignedIds, $existingAssignments, $roleIds, $shiftIds, $organizerId) {
            $campaign->update([
                'name' => $validated['campaign_name'],
                'location' => $validated['location'],
                'latitude' => null,
                'longitude' => null,
                'type' => $validated['campaign_type'],
                'target' => (float) $validated['funding_target'],
                'description' => $validated['description'] ?? null,
            ]);

            $selectedSkillIds = array_unique($validated['skills'] ?? []);
            CampaignSkill::where('campaign_id', $campaign->id)
                ->whereNotIn('skill_id', $selectedSkillIds ?: [0])
                ->delete();
            foreach ($selectedSkillIds as $skillId) {
                CampaignSkill::firstOrCreate([
                    'campaign_id' => $campaign->id,
                    'skill_id' => $skillId,
                ]);
            }

            foreach ($removeIds as $volunteerId) {
                VolunteerApplication::query()
                    ->where('campaign_id', $campaign->id)
                    ->where('user_id', $volunteerId)
                    ->where('status', 'Approved')
                    ->update(['status' => 'Withdrawn']);

                CampaignVolunteer::query()
                    ->where('campaign_id', $campaign->id)
                    ->where('volunteer_id', $volunteerId)
                    ->delete();
            }

            foreach ($selectedIds as $volunteerId) {
                if ($existingAssignments->has($volunteerId)) {
                    $existingAssignments[$volunteerId]->update([
                        'role_id' => $roleIds[$volunteerId],
                        'shift_id' => $shiftIds[$volunteerId],
                    ]);
                } else {
                    CampaignVolunteer::create([
                        'campaign_id' => $campaign->id,
                        'volunteer_id' => $volunteerId,
                        'role_id' => $roleIds[$volunteerId],
                        'shift_id' => $shiftIds[$volunteerId],
                    ]);
                }
            }

            if ($newlyAssignedIds->isNotEmpty()) {
                $notification = Notification::create([
                    'organizer_id' => $organizerId,
                    'title' => 'New Campaign Assignment',
                    'message' => 'You have been assigned to ' . $campaign->name . '. Please review your assignment details in the Volunteer Portal.',
                    'type' => 'System',
                    'audience' => 'Campaign Volunteers',
                    'campaign_id' => $campaign->id,
                    'status' => 'Sent',
                    'recipients' => $newlyAssignedIds->count(),
                ]);
                $now = now();
                UserNotification::insert($newlyAssignedIds->map(fn ($volunteerId) => [
                    'user_id' => $volunteerId,
                    'notification_id' => $notification->id,
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());
            }
        });

        ActivityLog::record('campaign.updated', "Updated campaign '{$campaign->name}'. Volunteer assignment records were synchronised without resetting attendance history.");

        return redirect('/campaign')->with('success', 'Campaign updated successfully. Existing volunteer assignment records have been preserved.');
    }

    public function applications($id)
    {
        $campaign = $this->findOwnedCampaign($id);

        $applications =
            VolunteerApplication::with('user')
                ->where('campaign_id', $id)
                ->latest()
                ->get();

        $roles =
            VolunteerRole::where(
                'status',
                'Active'
            )->get();

        $shifts =
            Shift::where(
                'status',
                'Active'
            )->get();

        return view(
            'organizer.applications',
            compact(
                'campaign',
                'applications',
                'roles',
                'shifts'
            )
        );
    }
    public function approveApplication(
        Request $request,
        $id
    ) {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', Rule::exists('volunteer_roles', 'id')->where('status', 'Active')],
            'shift_id' => ['required', 'integer', Rule::exists('shifts', 'id')->where('status', 'Active')],
        ]);

        $application = VolunteerApplication::whereHas('campaign', function ($query) {
            $query->where('organizer_id', session('user.id'));
        })->with(['user', 'campaign'])->findOrFail($id);

        if ($application->status !== 'Pending') {
            return back()->with('error', 'Only pending applications can be approved.');
        }

        if (!$application->user || $application->user->role !== 'volunteer') {
            return back()->with('error', 'The applicant is not a valid volunteer account.');
        }

        if (!(bool) ($application->user->is_active ?? true)
            || ($application->user->volunteer_status ?? 'Active') !== 'Active') {
            return back()->with('error', 'This volunteer account is inactive and cannot be assigned.');
        }

        // Do not allow an Organizer to approve a volunteer who is already
        // committed to an active campaign belonging to another Organizer.
        $organizerId = (int) session('user.id');
        $assignedToAnotherOrganizer = CampaignVolunteer::where('volunteer_id', $application->user_id)
            ->where('campaign_id', '!=', $application->campaign_id)
            ->whereHas('campaign', function ($query) use ($organizerId) {
                $query->where('organizer_id', '!=', $organizerId)
                    ->whereIn('status', ['Fundraising', 'Recruiting', 'In Progress']);
            })
            ->exists();

        if ($assignedToAnotherOrganizer) {
            return back()->with('error', 'This volunteer is already assigned to an active campaign managed by another organizer. The application cannot be approved while that assignment is active.');
        }

        $role = VolunteerRole::findOrFail($validated['role_id']);
        $shift = Shift::findOrFail($validated['shift_id']);

        DB::transaction(function () use ($application, $validated, $role, $shift, $organizerId) {
            $assignment = CampaignVolunteer::where('campaign_id', $application->campaign_id)
                ->where('volunteer_id', $application->user_id)
                ->first();

            if ($assignment) {
                $assignment->update([
                    'role_id' => $validated['role_id'],
                    'shift_id' => $validated['shift_id'],
                ]);
            } else {
                CampaignVolunteer::create([
                    'campaign_id' => $application->campaign_id,
                    'volunteer_id' => $application->user_id,
                    'role_id' => $validated['role_id'],
                    'shift_id' => $validated['shift_id'],
                ]);
            }

            $application->update(['status' => 'Approved']);

            $notification = Notification::create([
                'organizer_id' => $organizerId,
                'title' => 'Volunteer Application Approved',
                'message' => 'Your application for ' . $application->campaign->name . ' has been approved. Your assigned role is ' . $role->name . ' and your shift is ' . $shift->name . '.',
                'type' => 'System',
                'audience' => 'Campaign Volunteers',
                'campaign_id' => $application->campaign_id,
                'status' => 'Sent',
                'recipients' => 1,
            ]);

            UserNotification::create([
                'user_id' => $application->user_id,
                'notification_id' => $notification->id,
                'is_read' => false,
            ]);
        });

        ActivityLog::record('volunteer.application_approved', "Approved {$application->user->name}'s application for campaign '{$application->campaign->name}'.");

        return back()->with('success', 'Volunteer approved and assigned successfully.');
    }

    public function rejectApplication($id)
    {
        $application = VolunteerApplication::whereHas('campaign', function ($query) {
            $query->where('organizer_id', session('user.id'));
        })->with('campaign')->findOrFail($id);

        if ($application->status !== 'Pending') {
            return back()->with('error', 'Only pending applications can be rejected.');
        }

        DB::transaction(function () use ($application) {
            $application->update(['status' => 'Rejected']);

            $notification = Notification::create([
                'organizer_id' => (int) session('user.id'),
                'title' => 'Volunteer Application Update',
                'message' => 'Your application for ' . $application->campaign->name . ' was not approved. You can review other available campaigns in your volunteer portal.',
                'type' => 'System',
                'audience' => 'Campaign Volunteers',
                'campaign_id' => $application->campaign_id,
                'status' => 'Sent',
                'recipients' => 1,
            ]);

            UserNotification::create([
                'user_id' => $application->user_id,
                'notification_id' => $notification->id,
                'is_read' => false,
            ]);
        });

        ActivityLog::record('volunteer.application_rejected', "Rejected {$application->user->name}'s application for campaign '{$application->campaign->name}'.");

        return back()->with('success', 'Application rejected successfully.');
    }

    /** Show the assigned volunteers and attendance saved for the selected date. */
    public function attendance(Request $request, $id)
    {
        $campaign = $this->findOwnedCampaign($id);

        $request->validate([
            'date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $attendanceDate = $request->query('date', today()->toDateString());

        $volunteers = CampaignVolunteer::with(['volunteer', 'role', 'shift'])
            ->where('campaign_id', $campaign->id)
            ->whereHas('volunteer', function ($query) {
                $query->where('role', 'volunteer');
            })
            ->orderBy('id')
            ->get();

        $assignedVolunteerIds = $volunteers->pluck('volunteer_id')->all();
        $attendanceRecords = Attendance::where('campaign_id', $campaign->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->whereIn('user_id', $assignedVolunteerIds)
            ->get()
            ->keyBy('user_id');

        $attendanceSummary = [
            'present' => $attendanceRecords->where('status', 'Present')->count(),
            'absent' => $attendanceRecords->where('status', 'Absent')->count(),
            'not_recorded' => max(0, $volunteers->count() - $attendanceRecords->count()),
        ];

        return view('organizer.attendance', compact(
            'campaign', 'volunteers', 'attendanceDate', 'attendanceRecords', 'attendanceSummary'
        ));
    }

    /** Save Present/Absent attendance for volunteers assigned to this organizer's campaign only. */
    public function storeAttendance(Request $request, $campaignId)
    {
        $campaign = $this->findOwnedCampaign($campaignId);

        $validated = $request->validate([
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'attendance' => ['nullable', 'array'],
            'attendance.*.status' => ['nullable', Rule::in(['Present', 'Absent'])],
            'attendance.*.check_in' => ['nullable', 'date_format:H:i'],
            'attendance.*.check_out' => ['nullable', 'date_format:H:i'],
            'attendance.*.hours_served' => ['nullable', 'integer', 'min:0', 'max:24'],
        ]);

        $assignedVolunteerIds = CampaignVolunteer::where('campaign_id', $campaign->id)
            ->pluck('volunteer_id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $postedVolunteerIds = array_map('strval', array_keys($validated['attendance'] ?? []));
        $unknownVolunteerIds = array_diff($postedVolunteerIds, $assignedVolunteerIds);
        if (!empty($unknownVolunteerIds)) {
            abort(422, 'Attendance can only be recorded for volunteers assigned to this campaign.');
        }

        $errors = [];
        foreach (($validated['attendance'] ?? []) as $volunteerId => $record) {
            if (empty($record['status'])) {
                continue;
            }
            if ($record['status'] === 'Present') {
                if (empty($record['check_in'])) {
                    $errors["attendance.{$volunteerId}.check_in"] = 'Check-in time is required for a volunteer marked present.';
                }
                if (empty($record['check_out'])) {
                    $errors["attendance.{$volunteerId}.check_out"] = 'Check-out time is required for a volunteer marked present.';
                }
                if (!empty($record['check_in']) && !empty($record['check_out'])) {
                    $start = Carbon::createFromFormat('H:i', $record['check_in']);
                    $end = Carbon::createFromFormat('H:i', $record['check_out']);
                    if ($end->equalTo($start)) {
                        $errors["attendance.{$volunteerId}.check_out"] = 'Check-out time must be different from check-in time.';
                    }
                }
            }
        }

        if ($errors) {
            return back()->withErrors($errors)->withInput();
        }

        DB::transaction(function () use ($campaign, $validated, $assignedVolunteerIds) {
            foreach ($assignedVolunteerIds as $volunteerId) {
                $record = $validated['attendance'][(string) $volunteerId] ?? null;
                if (!$record) {
                    continue;
                }

                if (empty($record['status'])) {
                    Attendance::where('campaign_id', $campaign->id)
                        ->where('user_id', $volunteerId)
                        ->whereDate('attendance_date', $validated['attendance_date'])
                        ->delete();
                    continue;
                }

                $isPresent = $record['status'] === 'Present';
                $checkIn = $isPresent ? $record['check_in'] : null;
                $checkOut = $isPresent ? $record['check_out'] : null;
                $hours = 0;

                if ($isPresent) {
                    if (isset($record['hours_served']) && $record['hours_served'] !== '') {
                        $hours = (int) $record['hours_served'];
                    } else {
                        $start = Carbon::createFromFormat('H:i', $checkIn);
                        $end = Carbon::createFromFormat('H:i', $checkOut);
                        if ($end->lessThan($start)) {
                            $end->addDay();
                        }
                        $hours = (int) round($start->diffInMinutes($end) / 60);
                        $hours = min(24, max(0, $hours));
                    }
                }

                Attendance::updateOrCreate(
                    [
                        'campaign_id' => $campaign->id,
                        'user_id' => $volunteerId,
                        'attendance_date' => $validated['attendance_date'],
                    ],
                    [
                        'status' => $record['status'],
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'hours_served' => $hours,
                    ]
                );
            }
        });

        $recordedCount = count(array_filter($validated['attendance'] ?? [], fn ($record) => !empty($record['status'])));
        ActivityLog::record('attendance.saved', "Recorded attendance for {$recordedCount} volunteer(s) in campaign '{$campaign->name}' on {$validated['attendance_date']}.");

        return redirect('/campaign/attendance/' . $campaign->id . '?date=' . $validated['attendance_date'])
            ->with('success', 'Attendance saved successfully.');
    }

    /** Return campaigns owned by the currently logged-in organizer. */
    /**
     * Volunteers may be selected when they are free of another organizer's
     * active commitment, or when they already belong to this organizer's
     * campaigns. Current campaign assignments remain available in Edit.
     */
    private function eligibleVolunteersQuery(?int $currentCampaignId = null): \Illuminate\Database\Eloquent\Builder
    {
        $organizerId = (int) session('user.id');

        $eligible = User::query()->where('role', 'volunteer');

        if ($currentCampaignId !== null) {
            // On Edit, retain current assignees so their existing records are not
            // silently removed, even if their account was later deactivated.
            return $eligible->where(function ($query) use ($organizerId, $currentCampaignId) {
                $query->whereHas('campaignAssignments', function ($assignmentQuery) use ($currentCampaignId) {
                    $assignmentQuery->where('campaign_id', $currentCampaignId);
                })->orWhere(function ($newAssigneeQuery) use ($organizerId, $currentCampaignId) {
                    $newAssigneeQuery->where('is_active', true)
                        ->where(function ($statusQuery) {
                            $statusQuery->whereNull('volunteer_status')->orWhere('volunteer_status', 'Active');
                        })
                        ->whereDoesntHave('campaignAssignments', function ($assignmentQuery) use ($organizerId, $currentCampaignId) {
                            $assignmentQuery->where('campaign_id', '!=', $currentCampaignId)
                                ->whereHas('campaign', function ($campaignQuery) use ($organizerId) {
                                    $campaignQuery->where('organizer_id', '!=', $organizerId)
                                        ->whereIn('status', ['Fundraising', 'Recruiting', 'In Progress']);
                                });
                        });
                });
            })->orderBy('name');
        }

        return $eligible
            ->where('is_active', true)
            ->where(function ($statusQuery) {
                $statusQuery->whereNull('volunteer_status')->orWhere('volunteer_status', 'Active');
            })
            ->whereDoesntHave('campaignAssignments', function ($assignmentQuery) use ($organizerId) {
                $assignmentQuery->whereHas('campaign', function ($campaignQuery) use ($organizerId) {
                    $campaignQuery->where('organizer_id', '!=', $organizerId)
                        ->whereIn('status', ['Fundraising', 'Recruiting', 'In Progress']);
                });
            })
            ->orderBy('name');
    }

    private function ownedCampaignQuery()
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        return Campaign::query()->where('organizer_id', $organizerId);
    }

    /** Prevent organizers from viewing or changing another organizer's campaign. */
    private function findOwnedCampaign($id)
    {
        return $this->ownedCampaignQuery()->findOrFail($id);
    }
}