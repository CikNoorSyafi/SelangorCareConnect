<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\VolunteerRole;
use App\Models\Shift;
use App\Models\CampaignType;



class ParameterController extends Controller
{
    public function index()
    {
        $skills = Skill::latest()->get();

        $roles = VolunteerRole::latest()->get();

        $shifts = Shift::latest()->get();

        $campaignTypes =
            CampaignType::latest()
                ->get();

        return view(
            'organizer.parameters.index',
            compact(
                'skills',
                'roles',
                'shifts',
                'campaignTypes'
            )
        );
    }

    public function storeSkill(Request $request)
    {
        $request->validate([

            'name' => 'required|max:50',

            'description' => 'nullable|max:255',

            'status' => 'required'

        ]);

        Skill::create([

            'name' => $request->name,

            'description' => $request->description,

            'status' => $request->status

        ]);

        return redirect('/parameters')
            ->with(
                'success',
                'Skill created successfully.'
            );
    }
    public function updateSkill(
        Request $request,
        $id
    ) {
        $skill =
            Skill::findOrFail($id);

        $skill->update([

            'name' => $request->name,

            'description' => $request->description,

            'status' => $request->status

        ]);

        return redirect('/parameters')
            ->with(
                'success',
                'Skill updated successfully.'
            );
    }

    public function editSkill($id)
    {
        $skill = Skill::findOrFail($id);

        return view(
            'organizer.parameters.edit-skill',
            compact('skill')
        );
    }
    public function deleteSkill($id)
    {
        Skill::findOrFail($id)
            ->delete();

        return redirect('/parameters')
            ->with(
                'success',
                'Skill deleted successfully.'
            );
    }

    public function storeRole(Request $request)
    {
        VolunteerRole::create([

            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status

        ]);

        return redirect('/parameters#roles')
            ->with('success', 'Role created successfully.');
    }
    public function editRole($id)
    {
        $role = VolunteerRole::findOrFail($id);

        return view(
            'organizer.parameters.edit-role',
            compact('role')
        );
    }

    public function updateRole(Request $request, $id)
    {
        $role = VolunteerRole::findOrFail($id);

        $role->update([

            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status

        ]);

        return redirect('/parameters#roles')
            ->with('success', 'Role updated successfully.');
    }

    public function deleteRole($id)
    {
        VolunteerRole::findOrFail($id)->delete();

        return redirect('/parameters#roles')
            ->with('success', 'Role deleted successfully.');
    }

    public function storeShift(Request $request)
    {
        Shift::create([

            'name' => $request->name,

            'start_time' => $request->start_time,

            'end_time' => $request->end_time,

            'status' => $request->status

        ]);

        return redirect('/parameters#shifts')
            ->with(
                'success',
                'Shift created successfully.'
            );
    }
    public function editShift($id)
    {
        $shift = Shift::findOrFail($id);

        return view(
            'organizer.parameters.edit-shift',
            compact('shift')
        );
    }

    public function updateShift(
        Request $request,
        $id
    ) {
        $shift = Shift::findOrFail($id);

        $shift->update([

            'name' => $request->name,

            'start_time' => $request->start_time,

            'end_time' => $request->end_time,

            'status' => $request->status

        ]);

        return redirect('/parameters#shifts')
            ->with(
                'success',
                'Shift updated successfully.'
            );
    }

    public function deleteShift($id)
    {
        Shift::findOrFail($id)
            ->delete();

        return redirect('/parameters#shifts')
            ->with(
                'success',
                'Shift deleted successfully.'
            );
    }

    public function storeCampaignType(Request $request)
    {
        CampaignType::create([

            'name' => $request->name,

            'description' => $request->description,

            'status' => $request->status

        ]);

        return redirect('/parameters#campaign-types')
            ->with(
                'success',
                'Campaign Type created successfully.'
            );
    }

    public function editCampaignType($id)
    {
        $campaignType =
            CampaignType::findOrFail($id);

        return view(
            'organizer.parameters.edit-campaign-type',
            compact('campaignType')
        );
    }

    public function updateCampaignType(
        Request $request,
        $id
    ) {
        $campaignType =
            CampaignType::findOrFail($id);

        $campaignType->update([

            'name' => $request->name,

            'description' => $request->description,

            'status' => $request->status

        ]);

        return redirect('/parameters#campaign-types')
            ->with(
                'success',
                'Campaign Type updated successfully.'
            );
    }

    public function deleteCampaignType($id)
    {
        CampaignType::findOrFail($id)
            ->delete();

        return redirect('/parameters#campaign-types')
            ->with(
                'success',
                'Campaign Type deleted successfully.'
            );
    }

}