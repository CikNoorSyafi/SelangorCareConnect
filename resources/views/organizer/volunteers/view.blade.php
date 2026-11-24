@extends('layouts.organizer')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between"><div><h1 class="text-2xl font-bold text-slate-900">Volunteer Profile</h1><p class="mt-1 text-sm text-slate-500">Volunteer information and activity within your campaigns.</p></div><div class="flex gap-2"><a href="/volunteers/edit/{{ $volunteer->id }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Edit Profile</a><a href="/volunteers" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Back</a></div></div>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-2xl font-bold text-slate-600">{{ strtoupper(substr($volunteer->name, 0, 1)) }}</div>
            <div class="flex-1"><h2 class="text-xl font-bold text-slate-900">{{ $volunteer->name }}</h2><p class="text-sm text-slate-500">{{ $volunteer->email }}</p></div>
            @if(($volunteer->volunteer_status ?? 'Active') === 'Active')<span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Active</span>@else<span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600">Inactive</span>@endif
        </div>
        <div class="mt-6 grid grid-cols-1 gap-5 border-t border-slate-100 pt-5 sm:grid-cols-3">
            <div><p class="text-xs font-bold uppercase text-slate-400">Phone</p><p class="mt-1 text-sm text-slate-700">{{ $volunteer->phone ?: '-' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-slate-400">Age</p><p class="mt-1 text-sm text-slate-700">{{ $volunteer->age ?: '-' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-slate-400">Registered</p><p class="mt-1 text-sm text-slate-700">{{ optional($volunteer->created_at)->format('d M Y') ?: '-' }}</p></div>
        </div>
        @if($volunteer->notes)<div class="mt-5 border-t border-slate-100 pt-5"><p class="text-xs font-bold uppercase text-slate-400">Notes</p><p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $volunteer->notes }}</p></div>@endif
        <div class="mt-5 border-t border-slate-100 pt-5"><p class="mb-2 text-xs font-bold uppercase text-slate-400">Skills</p><div class="flex flex-wrap gap-2">@forelse($volunteer->volunteerSkills as $volunteerSkill)@if($volunteerSkill->skill)<span class="rounded-full bg-blue-50 px-3 py-1 text-sm text-blue-700">{{ $volunteerSkill->skill->name }}</span>@endif @empty<span class="text-sm text-slate-400">No skills recorded.</span>@endforelse</div></div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Campaign Assignments</h2>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50 text-left text-xs font-bold uppercase text-slate-500"><tr><th class="px-3 py-3">Campaign</th><th class="px-3 py-3">Role</th><th class="px-3 py-3">Shift</th><th class="px-3 py-3">Campaign Status</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($assignments as $assignment)<tr><td class="px-3 py-3 text-sm font-medium text-slate-800">{{ $assignment->campaign->name ?? '-' }}</td><td class="px-3 py-3 text-sm text-slate-600">{{ $assignment->role->name ?? '-' }}</td><td class="px-3 py-3 text-sm text-slate-600">{{ $assignment->shift->name ?? '-' }}</td><td class="px-3 py-3 text-sm text-slate-600">{{ $assignment->campaign->status ?? '-' }}</td></tr>@empty<tr><td colspan="4" class="px-3 py-8 text-center text-sm text-slate-500">No campaign assignments under your organizer account.</td></tr>@endforelse</tbody></table></div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Recent Attendance</h2>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50 text-left text-xs font-bold uppercase text-slate-500"><tr><th class="px-3 py-3">Campaign</th><th class="px-3 py-3">Date</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Hours</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($attendance as $record)<tr><td class="px-3 py-3 text-sm text-slate-700">{{ $record->campaign->name ?? '-' }}</td><td class="px-3 py-3 text-sm text-slate-600">{{ $record->attendance_date?->format('d M Y') }}</td><td class="px-3 py-3"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $record->status === 'Present' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $record->status }}</span></td><td class="px-3 py-3 text-sm text-slate-600">{{ $record->hours_served }} hrs</td></tr>@empty<tr><td colspan="4" class="px-3 py-8 text-center text-sm text-slate-500">No attendance records for your campaigns.</td></tr>@endforelse</tbody></table></div>
    </section>
</div>
@endsection
