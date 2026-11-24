@extends('layouts.organizer')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><h1 class="text-2xl font-bold text-slate-900">Volunteer Applications</h1><p class="mt-1 text-sm text-slate-500">Applications submitted to campaigns managed by your organizer account.</p></div><a href="/volunteers" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Back to Volunteers</a></div>
    @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="/volunteers/applications" class="grid grid-cols-1 gap-3 border-b border-slate-200 bg-slate-50 p-4 sm:grid-cols-3"><input name="search" value="{{ request('search') }}" placeholder="Search volunteer or campaign" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">All statuses</option>@foreach(['Pending','Approved','Rejected','Withdrawn'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select><div class="flex gap-2"><button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Filter</button><a href="/volunteers/applications" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-600">Clear</a></div></form>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-white text-left text-xs font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-4">Volunteer</th><th class="px-4 py-4">Campaign</th><th class="px-4 py-4">Requested Shift</th><th class="px-4 py-4">Skill / Notes</th><th class="px-4 py-4">Status</th><th class="px-4 py-4">Review</th></tr></thead><tbody class="divide-y divide-slate-100">
        @forelse($applications as $application)
            <tr class="align-top"><td class="px-4 py-4"><p class="font-semibold text-slate-800">{{ $application->user->name ?? 'Volunteer account unavailable' }}</p><p class="text-xs text-slate-500">{{ $application->user->email ?? '' }}</p></td><td class="px-4 py-4 text-sm text-slate-700">{{ $application->campaign->name ?? '-' }}</td><td class="px-4 py-4 text-sm text-slate-700">{{ $application->shift ?: '-' }}</td><td class="max-w-xs px-4 py-4 text-sm text-slate-600"><p>{{ $application->skill ?: '-' }}</p>@if($application->notes)<p class="mt-1 text-xs text-slate-400">{{ $application->notes }}</p>@endif</td><td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $application->status === 'Pending' ? 'bg-amber-50 text-amber-700' : ($application->status === 'Approved' ? 'bg-emerald-50 text-emerald-700' : ($application->status === 'Rejected' ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-600')) }}">{{ $application->status }}</span></td><td class="min-w-[260px] px-4 py-4">
                @if($application->status === 'Pending')
                    <form method="POST" action="{{ route('applications.approve', $application->id) }}" class="space-y-2">@csrf
                        <select name="role_id" required class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs"><option value="">Assign volunteer role</option>@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select>
                        <select name="shift_id" required class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs"><option value="">Assign shift</option>@foreach($shifts as $shift)<option value="{{ $shift->id }}">{{ $shift->name }}</option>@endforeach</select>
                        <div class="flex gap-2"><button class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">Approve & Assign</button></form>
                    <form method="POST" action="{{ route('applications.reject', $application->id) }}">@csrf<button class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">Reject</button></form></div>
                @elseif($application->status === 'Approved')<span class="text-xs font-semibold text-emerald-700">Assigned to campaign</span>
                @else<span class="text-xs text-slate-500">No action available</span>@endif
            </td></tr>
        @empty<tr><td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">No applications found for your campaigns.</td></tr>@endforelse
        </tbody></table></div>
        <div class="border-t border-slate-200 px-4 py-4">{{ $applications->links() }}</div>
    </section>
</div>
@endsection
