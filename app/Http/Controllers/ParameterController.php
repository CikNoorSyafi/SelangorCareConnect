<?php

namespace App\Http\Controllers;

use App\Models\CampaignType;
use App\Models\Shift;
use App\Models\Skill;
use App\Models\VolunteerRole;
use Illuminate\Http\Request;

class ParameterController extends Controller
{
    /**
     * Shared master data is managed centrally by System Administrator.
     */
    public function index()
    {
        $skills = Skill::latest()->get();
        $roles = VolunteerRole::latest()->get();
        $shifts = Shift::latest()->get();
        $campaignTypes = CampaignType::latest()->get();

        return view('admin.parameters.index', compact(
            'skills',
            'roles',
            'shifts',
            'campaignTypes'
        ));
    }

    // ---------------------------------------------------------------------
    // Skills
    // ---------------------------------------------------------------------

    public function storeSkill(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        Skill::create($validated);

        return redirect('/admin/parameters#skills')
            ->with('success', 'Skill created successfully.');
    }

    public function editSkill($id)
    {
        $skill = Skill::findOrFail($id);

        return view('admin.parameters.edit-skill', compact('skill'));
    }

    public function updateSkill(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        Skill::findOrFail($id)->update($validated);

        return redirect('/admin/parameters#skills')
            ->with('success', 'Skill updated successfully.');
    }

    public function deleteSkill($id)
    {
        Skill::findOrFail($id)->update(['status' => 'Inactive']);

        return redirect('/admin/parameters#skills')
            ->with('success', 'Skill deactivated successfully. Existing campaign and volunteer records are preserved.');
    }

    // ---------------------------------------------------------------------
    // Volunteer roles
    // ---------------------------------------------------------------------

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        VolunteerRole::create($validated);

        return redirect('/admin/parameters#roles')
            ->with('success', 'Volunteer role created successfully.');
    }

    public function editRole($id)
    {
        $role = VolunteerRole::findOrFail($id);

        return view('admin.parameters.edit-role', compact('role'));
    }

    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        VolunteerRole::findOrFail($id)->update($validated);

        return redirect('/admin/parameters#roles')
            ->with('success', 'Volunteer role updated successfully.');
    }

    public function deleteRole($id)
    {
        VolunteerRole::findOrFail($id)->update(['status' => 'Inactive']);

        return redirect('/admin/parameters#roles')
            ->with('success', 'Volunteer role deactivated successfully. Existing assignments are preserved.');
    }

    // ---------------------------------------------------------------------
    // Shifts
    // ---------------------------------------------------------------------

    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        Shift::create($validated);

        return redirect('/admin/parameters#shifts')
            ->with('success', 'Shift created successfully.');
    }

    public function editShift($id)
    {
        $shift = Shift::findOrFail($id);

        return view('admin.parameters.edit-shift', compact('shift'));
    }

    public function updateShift(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        Shift::findOrFail($id)->update($validated);

        return redirect('/admin/parameters#shifts')
            ->with('success', 'Shift updated successfully.');
    }

    public function deleteShift($id)
    {
        Shift::findOrFail($id)->update(['status' => 'Inactive']);

        return redirect('/admin/parameters#shifts')
            ->with('success', 'Shift deactivated successfully. Existing assignments are preserved.');
    }

    // ---------------------------------------------------------------------
    // Campaign types
    // ---------------------------------------------------------------------

    public function storeCampaignType(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        CampaignType::create($validated);

        return redirect('/admin/parameters#campaign-types')
            ->with('success', 'Campaign type created successfully.');
    }

    public function editCampaignType($id)
    {
        $campaignType = CampaignType::findOrFail($id);

        return view('admin.parameters.edit-campaign-type', compact('campaignType'));
    }

    public function updateCampaignType(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        CampaignType::findOrFail($id)->update($validated);

        return redirect('/admin/parameters#campaign-types')
            ->with('success', 'Campaign type updated successfully.');
    }

    public function deleteCampaignType($id)
    {
        CampaignType::findOrFail($id)->update(['status' => 'Inactive']);

        return redirect('/admin/parameters#campaign-types')
            ->with('success', 'Campaign type deactivated successfully. Existing campaigns are preserved.');
    }
}
