<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Skill;
use App\Models\VolunteerRole;
use App\Models\Shift;
use App\Models\CampaignVolunteer;
use App\Models\CampaignType;
use App\Models\CampaignSkill;


class CampaignController extends Controller
{
    public function index()
    {
        $volunteers = User::where(
            'role',
            'volunteer'
        )->get();

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

        $campaigns = Campaign::withCount('volunteers')
            ->when(
                $search,
                function ($query) use ($search) {

                    $query->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'location',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'status',
                            'like',
                            "%{$search}%"
                        );
                }
            )
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $totalCampaigns =
            Campaign::count();

        $activeCampaigns =
            Campaign::where(
                'status',
                'Approved'
            )->count();

        $pendingCampaigns =
            Campaign::where(
                'status',
                'Pending'
            )->count();

        $completedCampaigns =
            Campaign::where(
                'status',
                'Closed'
            )->count();

        $locations = [

            // Petaling
            'Petaling Jaya',
            'Subang Jaya',
            'Puchong',
            'Damansara',
            'Kelana Jaya',
            'Seri Kembangan',
            'Seri Petaling',

            // Klang
            'Klang',
            'Port Klang',
            'Bukit Tinggi',

            // Gombak
            'Gombak',
            'Selayang',
            'Batu Caves',
            'Rawang',

            // Hulu Langat
            'Kajang',
            'Cheras',
            'Semenyih',
            'Bandar Baru Bangi',
            'Balakong',
            'Ampang',

            // Kuala Langat
            'Banting',
            'Jenjarom',
            'Telok Panglima Garang',

            // Kuala Selangor
            'Kuala Selangor',
            'Bestari Jaya',
            'Jeram',
            'Ijok',

            // Hulu Selangor
            'Kuala Kubu Bharu',
            'Batang Kali',
            'Hulu Yam',
            'Kerling',

            // Sepang
            'Sepang',
            'Cyberjaya',
            'Putra Perdana',
            'Salak Tinggi',

            // Sabak Bernam
            'Sabak Bernam',
            'Sungai Besar',
            'Sekinchan',

            // Shah Alam
            'Shah Alam'

        ];

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
                'locations'
            )
        );
    }

    public function store(Request $request)
    {
        $campaign = Campaign::create([

            'name' => $request->campaign_name,

            'type' => $request->campaign_type,

            'location' => $request->location,

            'target' => floatval(
                str_replace(',', '', $request->funding_target)
            ),

            'start_date' => $request->start_date,

            'end_date' => $request->end_date,

            'description' => $request->description,

            'status' => 'Pending'

        ]);
        if ($request->has('skills')) {

            foreach ($request->skills as $skillId) {

                CampaignSkill::create([

                    'campaign_id' => $campaign->id,

                    'skill_id' => $skillId

                ]);
            }
        }

        if ($request->has('volunteers')) {

            foreach ($request->volunteers as $volunteerId) {

                CampaignVolunteer::create([

                    'campaign_id' => $campaign->id,

                    'volunteer_id' => $volunteerId,

                    'role_id' => $request->role_id[$volunteerId],

                    'shift_id' => $request->shift_id[$volunteerId]

                ]);
            }
        }

        return redirect('/campaign')
            ->with(
                'success',
                'Campaign created successfully.'
            );
    }

    public function delete($id)
    {
        Campaign::findOrFail($id)
            ->delete();

        return redirect('/campaign')
            ->with(
                'success',
                'Campaign deleted successfully.'
            );
    }

    public function edit($id)
    {
        $campaign =
            Campaign::findOrFail($id);

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

        if (!$campaign) {
            return redirect('/campaign');
        }

        $volunteers = User::where(
            'role',
            'volunteer'
        )->get();

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

        $locations = [

            'Petaling Jaya',
            'Subang Jaya',
            'Puchong',
            'Damansara',
            'Kelana Jaya',
            'Seri Kembangan',
            'Seri Petaling',

            'Klang',
            'Port Klang',
            'Bukit Tinggi',

            'Gombak',
            'Selayang',
            'Batu Caves',
            'Rawang',

            'Kajang',
            'Cheras',
            'Semenyih',
            'Bandar Baru Bangi',
            'Balakong',
            'Ampang',

            'Banting',
            'Jenjarom',
            'Telok Panglima Garang',

            'Kuala Selangor',
            'Bestari Jaya',
            'Jeram',
            'Ijok',

            'Kuala Kubu Bharu',
            'Batang Kali',
            'Hulu Yam',
            'Kerling',

            'Sepang',
            'Cyberjaya',
            'Putra Perdana',
            'Salak Tinggi',

            'Sabak Bernam',
            'Sungai Besar',
            'Sekinchan',

            'Shah Alam'

        ];

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
                'locations'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $campaign =
            Campaign::findOrFail($id);

        $campaign->update([

            'name' => $request->campaign_name,

            'location' => $request->location,
            'type' => $request->campaign_type,
            'target' => floatval(
                str_replace(
                    ',',
                    '',
                    $request->funding_target
                )
            ),

            'description' =>
                $request->description

        ]);
        CampaignSkill::where(
            'campaign_id',
            $campaign->id
        )->delete();

        if ($request->has('skills')) {

            foreach ($request->skills as $skillId) {

                CampaignSkill::create([

                    'campaign_id' => $campaign->id,

                    'skill_id' => $skillId

                ]);
            }
        }
        CampaignVolunteer::where(
            'campaign_id',
            $campaign->id
        )->delete();

        if ($request->has('volunteers')) {

            foreach ($request->volunteers as $volunteerId) {

                CampaignVolunteer::create([

                    'campaign_id' => $campaign->id,
                    'volunteer_id' => $volunteerId,
                    'role_id' => $request->role_id[$volunteerId],
                    'shift_id' => $request->shift_id[$volunteerId]

                ]);
            }
        }

        return redirect('/campaign')
            ->with(
                'success',
                'Campaign updated successfully.'
            );
    }
}