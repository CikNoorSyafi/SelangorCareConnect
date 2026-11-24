<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ActivityLog;
use App\Models\Campaign;
use App\Models\CampaignVolunteer;
use App\Models\Shift;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\Skill;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Models\VolunteerRole;
use App\Models\VolunteerSkill;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class VolunteerController extends Controller
{
    /** Campaign statuses that represent a current volunteer commitment. */
    private const ACTIVE_ASSIGNMENT_CAMPAIGN_STATUSES = [
        'Fundraising',
        'Recruiting',
        'In Progress',
    ];

    /**
     * Display the shared volunteer directory with organizer-scoped activity.
     */
    public function index(Request $request)
    {
        $organizerId = $this->organizerId();
        $ownedCampaignIds = Campaign::query()
            ->select('id')
            ->where('organizer_id', $organizerId);

        // Show volunteers who are not committed to an active campaign
        // managed by a different organizer. Volunteers already assigned to
        // this organizer's campaigns remain visible for ongoing management.
        $volunteersQuery = $this->visibleVolunteersQuery($organizerId)
            ->with(['volunteerSkills.skill'])
            ->withCount([
                'campaignAssignments as organizer_assignment_count' => function (Builder $query) use ($organizerId) {
                    $query->whereHas('campaign', function (Builder $campaignQuery) use ($organizerId) {
                        $campaignQuery->where('organizer_id', $organizerId);
                    });
                },
                'volunteerApplications',
                'campaignAssignments',
                'attendances',
            ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $volunteersQuery->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('volunteerSkills.skill', function (Builder $skillQuery) use ($search) {
                        $skillQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (in_array($request->input('status'), ['Active', 'Inactive'], true)) {
            $volunteersQuery->where('volunteer_status', $request->input('status'));
        }

        if ($request->input('assignment_status') === 'Assigned') {
            $volunteersQuery->whereHas('campaignAssignments', function (Builder $query) use ($organizerId) {
                $query->whereHas('campaign', function (Builder $campaignQuery) use ($organizerId) {
                    $campaignQuery->where('organizer_id', $organizerId);
                });
            });
        } elseif ($request->input('assignment_status') === 'Available') {
            // Available means no current assignment under any organizer.
            // Completed campaigns are historical participation, not active commitments.
            $volunteersQuery->whereDoesntHave('campaignAssignments', function (Builder $query) {
                $query->whereHas('campaign', function (Builder $campaignQuery) {
                    $campaignQuery->whereIn('status', self::ACTIVE_ASSIGNMENT_CAMPAIGN_STATUSES);
                });
            });
        }

        if ($request->input('activity') === 'active_this_month') {
            $volunteersQuery->whereIn('id', Attendance::query()
                ->select('user_id')
                ->where('status', 'Present')
                ->whereBetween('attendance_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->whereIn('campaign_id', (clone $ownedCampaignIds))
                ->distinct());
        }

        $volunteers = $volunteersQuery
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Keep the directory KPI consistent with the volunteers actually
        // visible to this organizer (excludes active assignments owned elsewhere).
        $totalVolunteers = (clone $this->visibleVolunteersQuery($organizerId))->count();

        $activeThisMonth = Attendance::query()
            ->where('status', 'Present')
            ->whereBetween('attendance_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->whereIn('campaign_id', (clone $ownedCampaignIds))
            ->distinct('user_id')
            ->count('user_id');

        $pendingApprovals = VolunteerApplication::query()
            ->where('status', 'Pending')
            ->whereIn('campaign_id', (clone $ownedCampaignIds))
            ->count();

        $inactiveVolunteers = (clone $this->visibleVolunteersQuery($organizerId))
            ->where('volunteer_status', 'Inactive')
            ->count();

        return view('organizer.volunteers.index', compact(
            'volunteers',
            'totalVolunteers',
            'activeThisMonth',
            'pendingApprovals',
            'inactiveVolunteers'
        ));
    }

    public function create()
    {
        $skills = Skill::where('status', 'Active')->orderBy('name')->get();

        return view('organizer.volunteers.create', compact('skills'));
    }

    /** Create a real volunteer user account and persist selected skills. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'age' => ['nullable', 'integer', 'min:13', 'max:120'],
            'volunteer_status' => ['required', Rule::in(['Active', 'Inactive'])],
            'notes' => ['nullable', 'string', 'max:3000'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', Rule::exists('skills', 'id')->where('status', 'Active')],
        ]);

        DB::transaction(function () use ($validated) {
            $volunteer = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'age' => $validated['age'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'volunteer_status' => $validated['volunteer_status'],
                'password' => Hash::make($validated['password']),
                'role' => 'volunteer',
            ]);

            foreach (array_unique($validated['skills'] ?? []) as $skillId) {
                $volunteer->volunteerSkills()->create(['skill_id' => $skillId]);
            }
        });

        ActivityLog::record('volunteer.registered', "Registered volunteer '{$validated['name']}' ({$validated['email']}).");

        return redirect('/volunteers')->with('success', 'Volunteer account registered successfully.');
    }

    public function view($id)
    {
        $organizerId = $this->organizerId();
        $volunteer = $this->visibleVolunteersQuery($organizerId)
            ->with(['volunteerSkills.skill'])
            ->findOrFail($id);

        $assignments = CampaignVolunteer::with(['campaign', 'role', 'shift'])
            ->where('volunteer_id', $id)
            ->whereHas('campaign', fn (Builder $query) => $query->where('organizer_id', $organizerId))
            ->latest()
            ->get();

        $attendance = Attendance::with('campaign')
            ->where('user_id', $id)
            ->whereIn('campaign_id', Campaign::select('id')->where('organizer_id', $organizerId))
            ->latest('attendance_date')
            ->take(10)
            ->get();

        return view('organizer.volunteers.view', compact('volunteer', 'assignments', 'attendance'));
    }

    public function edit($id)
    {
        $organizerId = $this->organizerId();
        $volunteer = $this->visibleVolunteersQuery($organizerId)
            ->with(['volunteerSkills.skill'])
            ->findOrFail($id);

        $assignments = CampaignVolunteer::with(['campaign', 'role', 'shift'])
            ->where('volunteer_id', $id)
            ->whereHas('campaign', fn (Builder $query) => $query->where('organizer_id', $organizerId))
            ->latest()
            ->get();

        $skills = Skill::where('status', 'Active')->orderBy('name')->get();
        $roles = VolunteerRole::where('status', 'Active')->orderBy('name')->get();
        $shifts = Shift::where('status', 'Active')->orderBy('name')->get();

        $assignedCampaignIds = CampaignVolunteer::where('volunteer_id', $id)
            ->whereHas('campaign', fn (Builder $query) => $query->where('organizer_id', $organizerId))
            ->select('campaign_id');

        $campaigns = $this->ownedCampaignQuery()
            ->whereIn('status', ['Fundraising', 'Recruiting', 'In Progress'])
            ->whereNotIn('id', $assignedCampaignIds)
            ->orderBy('name')
            ->get();

        return view('organizer.volunteers.edit', compact(
            'volunteer', 'assignments', 'skills', 'roles', 'shifts', 'campaigns'
        ));
    }

    /** Update volunteer account information. */
    public function update(Request $request, $id)
    {
        $volunteer = $this->visibleVolunteersQuery($this->organizerId())->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($volunteer->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'age' => ['nullable', 'integer', 'min:13', 'max:120'],
            'volunteer_status' => ['required', Rule::in(['Active', 'Inactive'])],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $volunteer->update($validated);
        ActivityLog::record('volunteer.profile_updated', "Updated volunteer profile for '{$volunteer->name}'.");

        return redirect('/volunteers/edit/' . $volunteer->id)->with('success', 'Volunteer profile updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'volunteer_status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $volunteer = $this->visibleVolunteersQuery($this->organizerId())->findOrFail($id);

        if ($validated['volunteer_status'] === 'Inactive') {
            $hasActiveAssignment = CampaignVolunteer::query()
                ->where('volunteer_id', $volunteer->id)
                ->whereHas('campaign', function (Builder $query) {
                    $query->whereIn('status', self::ACTIVE_ASSIGNMENT_CAMPAIGN_STATUSES);
                })
                ->exists();

            if ($hasActiveAssignment) {
                return back()->with('error', 'This volunteer is assigned to an active campaign. Remove or complete the assignment before deactivating the account.');
            }
        }

        $volunteer->update($validated);
        ActivityLog::record('volunteer.status_changed', "Changed volunteer '{$volunteer->name}' account status to {$validated['volunteer_status']}.");
        return back()->with('success', 'Volunteer status updated successfully.');
    }

    /** Preserve historical records: deletion is allowed only before any participation history exists. */
    public function delete($id)
    {
        $volunteer = $this->visibleVolunteersQuery($this->organizerId())->findOrFail($id);

        $hasHistory = $volunteer->volunteerApplications()->exists()
            || $volunteer->campaignAssignments()->exists()
            || $volunteer->attendances()->exists();

        if ($hasHistory) {
            return back()->with('error', 'This volunteer has application, assignment or attendance history. Set the account to Inactive instead to preserve those records.');
        }

        $volunteerName = $volunteer->name;
        DB::transaction(function () use ($volunteer) {
            $volunteer->volunteerSkills()->delete();
            $volunteer->delete();
        });
        ActivityLog::record('volunteer.deleted', "Deleted volunteer account '{$volunteerName}'.");

        return redirect('/volunteers')->with('success', 'Volunteer account deleted successfully.');
    }

    public function storeSkill(Request $request)
    {
        $validated = $request->validate([
            'volunteer_id' => ['required', 'integer'],
            'skill_id' => ['required', 'integer', Rule::exists('skills', 'id')->where('status', 'Active')],
        ]);

        $volunteer = $this->visibleVolunteersQuery($this->organizerId())->findOrFail($validated['volunteer_id']);
        $volunteer->volunteerSkills()->firstOrCreate(['skill_id' => $validated['skill_id']]);

        return back()->with('success', 'Volunteer skill saved successfully.');
    }

    public function deleteSkill($id)
    {
        $volunteerSkill = VolunteerSkill::with('volunteer')->findOrFail($id);
        abort_unless($volunteerSkill->volunteer && $volunteerSkill->volunteer->role === 'volunteer', 404);
        $this->visibleVolunteersQuery($this->organizerId())->findOrFail($volunteerSkill->volunteer_id);
        $volunteerSkill->delete();

        return back()->with('success', 'Volunteer skill removed successfully.');
    }

    public function updateAssignment(Request $request, $id)
    {
        $assignment = CampaignVolunteer::with(['campaign', 'volunteer'])
            ->whereHas('campaign', function (Builder $query) {
                $query->where('organizer_id', $this->organizerId())
                    ->whereIn('status', self::ACTIVE_ASSIGNMENT_CAMPAIGN_STATUSES);
            })->findOrFail($id);

        $validated = $request->validate([
            'role_id' => ['required', 'integer', Rule::exists('volunteer_roles', 'id')->where('status', 'Active')],
            'shift_id' => ['required', 'integer', Rule::exists('shifts', 'id')->where('status', 'Active')],
        ]);

        if (Attendance::where('campaign_id', $assignment->campaign_id)
            ->where('user_id', $assignment->volunteer_id)
            ->exists()) {
            return back()->with('error', 'This volunteer has attendance history. The assigned role and shift can no longer be changed.');
        }

        $assignment->update($validated);

        return back()->with('success', 'Volunteer campaign assignment updated successfully.');
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => ['required', 'integer'],
            'volunteer_id' => ['required', 'integer'],
            'role_id' => ['required', 'integer', Rule::exists('volunteer_roles', 'id')->where('status', 'Active')],
            'shift_id' => ['required', 'integer', Rule::exists('shifts', 'id')->where('status', 'Active')],
        ]);

        $campaign = $this->ownedCampaignQuery()
            ->whereIn('status', ['Fundraising', 'Recruiting', 'In Progress'])
            ->findOrFail($validated['campaign_id']);
        $volunteer = User::query()
            ->where('role', 'volunteer')
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('volunteer_status')->orWhere('volunteer_status', 'Active');
            })
            ->findOrFail($validated['volunteer_id']);

        $exists = CampaignVolunteer::where('campaign_id', $campaign->id)
            ->where('volunteer_id', $volunteer->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This volunteer is already assigned to the selected campaign.');
        }

        $organizerId = $this->organizerId();
        $assignedToAnotherOrganizer = CampaignVolunteer::where('volunteer_id', $volunteer->id)
            ->whereHas('campaign', function (Builder $query) use ($organizerId) {
                $query->where('organizer_id', '!=', $organizerId)
                    ->whereIn('status', self::ACTIVE_ASSIGNMENT_CAMPAIGN_STATUSES);
            })
            ->exists();

        if ($assignedToAnotherOrganizer) {
            return back()->with('error', 'This volunteer is already assigned to an active campaign managed by another organizer and is not available.');
        }

        DB::transaction(function () use ($campaign, $volunteer, $validated, $organizerId) {
            CampaignVolunteer::create([
                'campaign_id' => $campaign->id,
                'volunteer_id' => $volunteer->id,
                'role_id' => $validated['role_id'],
                'shift_id' => $validated['shift_id'],
            ]);

            $role = VolunteerRole::findOrFail($validated['role_id']);
            $shift = Shift::findOrFail($validated['shift_id']);
            $notification = Notification::create([
                'organizer_id' => $organizerId,
                'title' => 'New Volunteer Assignment',
                'message' => 'You have been assigned to ' . $campaign->name . ' as ' . $role->name . ' for the ' . $shift->name . ' shift.',
                'type' => 'System',
                'audience' => 'Campaign Volunteers',
                'campaign_id' => $campaign->id,
                'status' => 'Sent',
                'recipients' => 1,
            ]);

            UserNotification::create([
                'user_id' => $volunteer->id,
                'notification_id' => $notification->id,
                'is_read' => false,
            ]);
        });

        ActivityLog::record('volunteer.assigned', "Assigned '{$volunteer->name}' to campaign '{$campaign->name}'.");
        return back()->with('success', 'Volunteer assigned to the campaign successfully. The volunteer has been notified.');
    }

    public function deleteAssignment($id)
    {
        $assignment = CampaignVolunteer::whereHas('campaign', function (Builder $query) {
            $query->where('organizer_id', $this->organizerId());
        })->findOrFail($id);

        if (Attendance::where('campaign_id', $assignment->campaign_id)
            ->where('user_id', $assignment->volunteer_id)
            ->exists()) {
            return back()->with('error', 'This assignment has attendance history and cannot be removed.');
        }

        $assignment->load(['campaign', 'volunteer']);
        $organizerId = $this->organizerId();
        DB::transaction(function () use ($assignment, $organizerId) {
            // Keep the application lifecycle consistent when an approved
            // applicant's assignment is removed by the organizer.
            VolunteerApplication::where('campaign_id', $assignment->campaign_id)
                ->where('user_id', $assignment->volunteer_id)
                ->where('status', 'Approved')
                ->update(['status' => 'Withdrawn']);

            $assignment->delete();

            if ($assignment->volunteer && $assignment->campaign) {
                $notification = Notification::create([
                    'organizer_id' => $organizerId,
                    'title' => 'Campaign Assignment Removed',
                    'message' => 'Your assignment for ' . $assignment->campaign->name . ' has been removed by the organizer.',
                    'type' => 'System',
                    'audience' => 'Campaign Volunteers',
                    'campaign_id' => $assignment->campaign_id,
                    'status' => 'Sent',
                    'recipients' => 1,
                ]);

                UserNotification::create([
                    'user_id' => $assignment->volunteer_id,
                    'notification_id' => $notification->id,
                    'is_read' => false,
                ]);
            }
        });

        ActivityLog::record('volunteer.assignment_removed', "Removed '{$assignment->volunteer->name}' from campaign '{$assignment->campaign->name}'.");
        return back()->with('success', 'Volunteer campaign assignment removed successfully. The volunteer has been notified.');
    }

    /** Show all applications submitted to campaigns owned by this organizer. */
    public function applications(Request $request)
    {
        $organizerId = $this->organizerId();
        $applicationsQuery = VolunteerApplication::with(['user', 'campaign'])
            ->whereHas('campaign', fn (Builder $query) => $query->where('organizer_id', $organizerId));

        if (in_array($request->input('status'), ['Pending', 'Approved', 'Rejected', 'Withdrawn'], true)) {
            $applicationsQuery->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $applicationsQuery->where(function (Builder $query) use ($search) {
                $query->whereHas('user', function (Builder $userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('campaign', function (Builder $campaignQuery) use ($search) {
                    $campaignQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $applications = $applicationsQuery->latest()->paginate(10)->withQueryString();
        $roles = VolunteerRole::where('status', 'Active')->orderBy('name')->get();
        $shifts = Shift::where('status', 'Active')->orderBy('name')->get();
        $pendingApprovals = VolunteerApplication::where('status', 'Pending')
            ->whereHas('campaign', fn (Builder $query) => $query->where('organizer_id', $organizerId))
            ->count();

        return view('organizer.volunteers.applications', compact('applications', 'roles', 'shifts', 'pendingApprovals'));
    }

    /**
     * Return volunteers relevant to this organizer:
     * - always include volunteers assigned to at least one of this organizer's campaigns;
     * - otherwise include volunteers who have no active commitment under another organizer;
     * - hide volunteers whose only active campaign commitment is with another organizer.
     *
     * This OR condition is important: a volunteer enrolled in this organizer's
     * campaign must remain visible even if they also have another assignment.
     */
    private function visibleVolunteersQuery(int $organizerId): Builder
    {
        return User::query()
            ->where('role', 'volunteer')
            ->where(function (Builder $visibilityQuery) use ($organizerId) {
                $visibilityQuery
                    ->whereHas('campaignAssignments', function (Builder $assignmentQuery) use ($organizerId) {
                        $assignmentQuery->whereHas('campaign', function (Builder $campaignQuery) use ($organizerId) {
                            $campaignQuery->where('organizer_id', $organizerId);
                        });
                    })
                    ->orWhereDoesntHave('campaignAssignments', function (Builder $assignmentQuery) use ($organizerId) {
                        $assignmentQuery->whereHas('campaign', function (Builder $campaignQuery) use ($organizerId) {
                            $campaignQuery->where('organizer_id', '!=', $organizerId)
                                ->whereIn('status', self::ACTIVE_ASSIGNMENT_CAMPAIGN_STATUSES);
                        });
                    });
            });
    }

    private function organizerId(): int
    {
        $organizerId = (int) session('user.id');
        abort_unless($organizerId > 0, 403, 'Organizer session not found.');

        return $organizerId;
    }

    private function ownedCampaignQuery(): Builder
    {
        return Campaign::query()->where('organizer_id', $this->organizerId());
    }
}
