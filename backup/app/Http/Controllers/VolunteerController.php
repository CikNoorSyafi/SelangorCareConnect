<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CampaignVolunteer;
use App\Models\Skill;
use App\Models\VolunteerSkill;
use App\Models\VolunteerRole;
use App\Models\Shift;
use App\Models\Campaign;


class VolunteerController extends Controller
{


    public function index(Request $request)
    {
        $volunteers = User::with(
            'volunteerSkills.skill'
        )
            ->where(
                'role',
                'volunteer'
            )
            ->paginate(5)
            ->withQueryString();

        // SEARCH FILTER
        if ($request->search) {

            $search = $request->search;

            $volunteers = User::with(
                'volunteerSkills.skill'
            )
                ->where(
                    'role',
                    'volunteer'
                )
                ->where(function ($query) use ($search) {

                    $query->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$search}%"
                        );

                })
                ->paginate(5)
                ->withQueryString();

        } else {

            $volunteers = User::with(
                'volunteerSkills.skill'
            )
                ->where(
                    'role',
                    'volunteer'
                )
                ->paginate(5)
                ->withQueryString();

        }

        // STATUS FILTER
        if ($request->status) {

            $volunteers = $volunteers->filter(function ($v) use ($request) {

                return $v['status'] == $request->status;

            });

        }

        // DASHBOARD COUNTS
        $totalVolunteers = User::where(
            'role',
            'volunteer'
        )->count();

        $activeThisMonth = User::where(
            'role',
            'volunteer'
        )->count();

        $pendingApprovals = 0;

        $inactiveVolunteers = 0;

        return view(
            'organizer.volunteers.index',
            compact(
                'volunteers',
                'totalVolunteers',
                'activeThisMonth',
                'pendingApprovals',
                'inactiveVolunteers'
            )
        );

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

    public function delete($id)
    {
        return redirect('/volunteers')
            ->with('success', 'Volunteer deleted (mock)');
    }

    public function view($id)
    {
        $volunteer = User::with(
            'volunteerSkills.skill'
        )->findOrFail($id);

        $assignments = CampaignVolunteer::with([
            'campaign',
            'role',
            'shift'
        ])
            ->where(
                'volunteer_id',
                $id
            )
            ->get();

        $skills = Skill::where(
            'status',
            'Active'
        )->get();

        return view(
            'organizer.volunteers.view',
            compact(
                'volunteer',
                'assignments',
                'skills'
            )
        );
    }

    public function storeSkill(Request $request)
    {
        $exists = VolunteerSkill::where(
            'volunteer_id',
            $request->volunteer_id
        )
            ->where(
                'skill_id',
                $request->skill_id
            )
            ->exists();

        if (!$exists) {

            VolunteerSkill::create([

                'volunteer_id' => $request->volunteer_id,

                'skill_id' => $request->skill_id

            ]);
        }

        return back()
            ->with(
                'success',
                'Skill assigned successfully.'
            );
    }
    public function deleteSkill($id)
    {
        VolunteerSkill::findOrFail($id)
            ->delete();

        return back()
            ->with(
                'success',
                'Skill removed successfully.'
            );
    }

    public function update(
        Request $request,
        $id
    ) {

        // Update campaign assignments
        if (
            isset($request->role_id) &&
            is_array($request->role_id)
        ) {

            foreach (
                $request->role_id as $assignmentId => $roleId
            ) {

                CampaignVolunteer::where(
                    'id',
                    $assignmentId
                )->update([

                            'role_id' => $roleId,

                            'shift_id' =>
                                $request->shift_id[$assignmentId]

                        ]);

            }

        }

        // Add new skill
        if (!empty($request->new_skill_id)) {

            $exists = VolunteerSkill::where(
                'volunteer_id',
                $id
            )
                ->where(
                    'skill_id',
                    $request->new_skill_id
                )
                ->exists();

            if (!$exists) {

                VolunteerSkill::create([

                    'volunteer_id' => $id,

                    'skill_id' => $request->new_skill_id

                ]);
            }
        }

        if (
            !empty($request->new_campaign_id)
        ) {

            $exists =
                CampaignVolunteer::where(
                    'campaign_id',
                    $request->campaign_id
                )
                    ->where(
                        'volunteer_id',
                        $id
                    )
                    ->exists();

            if (!$exists) {

                CampaignVolunteer::create([

                    'campaign_id' =>
                        $request->new_campaign_id,

                    'volunteer_id' =>
                        $id,

                    'role_id' =>
                        $request->new_role_id,

                    'shift_id' =>
                        $request->new_shift_id

                ]);
            }
        }

        return redirect(
            '/volunteers/edit/' . $id
        )->with(
                'success',
                'Volunteer details updated successfully.'
            );
    }

    public function edit($id)
    {
        $volunteer = User::with(
            'volunteerSkills.skill'
        )->findOrFail($id);

        $assignments = CampaignVolunteer::with([
            'campaign',
            'role',
            'shift'
        ])
            ->where(
                'volunteer_id',
                $id
            )
            ->get();

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

        $assignedCampaignIds = CampaignVolunteer::where(
            'volunteer_id',
            $id
        )
            ->pluck('campaign_id');

        $campaigns = Campaign::whereIn(
            'status',
            ['Approved', 'Pending']
        )
            ->whereNotIn(
                'id',
                $assignedCampaignIds
            )
            ->get();

        return view(
            'organizer.volunteers.edit',
            compact(
                'volunteer',
                'assignments',
                'skills',
                'roles',
                'shifts',
                'campaigns'
            )
        );
    }
    public function storeAssignment(Request $request)
    {
        $exists = CampaignVolunteer::where(
            'campaign_id',
            $request->campaign_id
        )
            ->where(
                'volunteer_id',
                $request->volunteer_id
            )
            ->exists();

        if ($exists) {

            return back()->with(
                'error',
                'Volunteer already assigned to this campaign.'
            );
        }

        CampaignVolunteer::create([

            'campaign_id' => $request->campaign_id,

            'volunteer_id' => $request->volunteer_id,

            'role_id' => $request->role_id,

            'shift_id' => $request->shift_id

        ]);

        return back()->with(
            'success',
            'Campaign assignment added successfully.'
        );
    }

    public function deleteAssignment($id)
    {
        CampaignVolunteer::findOrFail($id)
            ->delete();

        return back()->with(
            'success',
            'Campaign assignment removed successfully.'
        );
    }

}