@extends('layouts.organizer')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between"><div><h1 class="text-2xl font-bold text-slate-900">Edit Volunteer</h1><p class="mt-1 text-sm text-slate-500">Update volunteer details, skills and campaign assignments.</p></div><a href="/volunteers" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Back to Volunteers</a></div>

    @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Volunteer Profile</h2>
        <form method="POST" action="/volunteers/update/{{ $volunteer->id }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @csrf
            <label class="text-sm font-medium text-slate-700">Full Name<input name="name" value="{{ old('name', $volunteer->name) }}" required maxlength="255" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></label>
            <label class="text-sm font-medium text-slate-700">Email<input type="email" name="email" value="{{ old('email', $volunteer->email) }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></label>
            <label class="text-sm font-medium text-slate-700">Phone Number<input name="phone" value="{{ old('phone', $volunteer->phone) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></label>
            <label class="text-sm font-medium text-slate-700">Age<input type="number" name="age" min="13" max="120" value="{{ old('age', $volunteer->age) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></label>
            <label class="text-sm font-medium text-slate-700">Account Status<select name="volunteer_status" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"><option value="Active" @selected(old('volunteer_status', $volunteer->volunteer_status ?? 'Active') === 'Active')>Active</option><option value="Inactive" @selected(old('volunteer_status', $volunteer->volunteer_status) === 'Inactive')>Inactive</option></select></label>
            <label class="text-sm font-medium text-slate-700 sm:col-span-2">Additional Notes<textarea name="notes" rows="3" maxlength="3000" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('notes', $volunteer->notes) }}</textarea></label>
            <div class="flex justify-end sm:col-span-2"><button class="rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-700">Save Profile</button></div>
        </form>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-1 text-lg font-semibold text-slate-800">Volunteer Skills</h2><p class="mb-4 text-sm text-slate-500">Skills are linked to the active Skills master list.</p>
        <div class="mb-5 flex flex-wrap gap-2">
            @forelse($volunteer->volunteerSkills as $volunteerSkill)
                @if($volunteerSkill->skill)
                    <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">
                        {{ $volunteerSkill->skill->name }}
                        <form method="POST" action="/volunteers/skills/delete/{{ $volunteerSkill->id }}" onsubmit="return confirm('Remove this skill?')">@csrf<button type="submit" aria-label="Remove {{ $volunteerSkill->skill->name }}" class="font-bold text-blue-700 hover:text-red-600">&times;</button></form>
                    </span>
                @endif
            @empty
                <p class="text-sm text-slate-400">No skills assigned yet.</p>
            @endforelse
        </div>
        <form method="POST" action="/volunteers/skills/store" class="flex flex-col gap-3 sm:flex-row">
            @csrf<input type="hidden" name="volunteer_id" value="{{ $volunteer->id }}">
            <select name="skill_id" required class="flex-1 rounded-lg border border-slate-300 px-3 py-2"><option value="">Select an active skill</option>@foreach($skills as $skill)<option value="{{ $skill->id }}">{{ $skill->name }}</option>@endforeach</select>
            <button class="rounded-lg bg-slate-800 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-900">Add Skill</button>
        </form>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-1 text-lg font-semibold text-slate-800">Campaign Assignments</h2><p class="mb-4 text-sm text-slate-500">Only assignments for campaigns owned by your organizer account are displayed here.</p>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-3 py-3">Campaign</th><th class="px-3 py-3">Role</th><th class="px-3 py-3">Shift</th><th class="px-3 py-3">Action</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($assignments as $assignment)
                    <tr>
                        <td class="px-3 py-3 text-sm font-medium text-slate-800">{{ $assignment->campaign->name ?? 'Campaign unavailable' }}</td>
                        <td class="px-3 py-3">
                            <form id="assignment-update-{{ $assignment->id }}" method="POST" action="/volunteers/assignment/update/{{ $assignment->id }}">@csrf
                                <select name="role_id" required class="w-full rounded-lg border border-slate-300 px-2 py-2 text-sm"><option value="{{ $assignment->role_id }}">{{ $assignment->role->name ?? 'Current role' }}</option>@foreach($roles->where('id', '!=', $assignment->role_id) as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select>
                            </form>
                        </td>
                        <td class="px-3 py-3"><select form="assignment-update-{{ $assignment->id }}" name="shift_id" required class="w-full rounded-lg border border-slate-300 px-2 py-2 text-sm"><option value="{{ $assignment->shift_id }}">{{ $assignment->shift->name ?? 'Current shift' }}</option>@foreach($shifts->where('id', '!=', $assignment->shift_id) as $shift)<option value="{{ $shift->id }}">{{ $shift->name }}</option>@endforeach</select></td>
                        <td class="whitespace-nowrap px-3 py-3"><div class="flex gap-2"><button form="assignment-update-{{ $assignment->id }}" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white">Save</button><form method="POST" action="/volunteers/assignment/delete/{{ $assignment->id }}" onsubmit="return confirm('Remove this assignment?')">@csrf<button class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">Remove</button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-8 text-center text-sm text-slate-500">This volunteer has no assignments to your campaigns.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 border-t border-slate-100 pt-5">
            <h3 class="mb-3 font-semibold text-slate-800">Add New Campaign Assignment</h3>
            @if(($volunteer->volunteer_status ?? 'Active') !== 'Active')
                <p class="rounded-lg bg-amber-50 p-3 text-sm text-amber-800">This volunteer is inactive. Activate the account before assigning it to another campaign.</p>
            @elseif($campaigns->isEmpty())
                <p class="rounded-lg bg-slate-50 p-3 text-sm text-slate-500">No eligible unassigned campaigns are available.</p>
            @else
                <form method="POST" action="/volunteers/assignment/store" class="grid grid-cols-1 gap-3 md:grid-cols-4">@csrf
                    <input type="hidden" name="volunteer_id" value="{{ $volunteer->id }}">
                    <select name="campaign_id" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Select campaign</option>@foreach($campaigns as $campaign)<option value="{{ $campaign->id }}">{{ $campaign->name }}</option>@endforeach</select>
                    <select name="role_id" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Select role</option>@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select>
                    <select name="shift_id" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">Select shift</option>@foreach($shifts as $shift)<option value="{{ $shift->id }}">{{ $shift->name }}</option>@endforeach</select>
                    <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Assign Volunteer</button>
                </form>
            @endif
        </div>
    </section>
</div>
@endsection
