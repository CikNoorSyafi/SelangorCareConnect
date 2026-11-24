@extends('layouts.organizer')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Volunteer Management</h1>
            <p class="mt-1 text-sm text-slate-500">Manage volunteers available to your campaigns and volunteers enrolled in your campaigns. Volunteers assigned only to another organizer’s active campaigns are not shown.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="/volunteers/applications" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Review Applications</a>
            <a href="/volunteers/create" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">+ Register Volunteer</a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="/volunteers" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="mb-3 flex items-center justify-between"><span class="material-symbols-outlined rounded-lg bg-blue-50 p-2 text-blue-600">groups</span><span class="text-xs font-semibold text-slate-400">Directory</span></div>
            <p class="text-sm font-medium text-slate-500">Total Volunteers</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($totalVolunteers) }}</p>
        </a>
        <a href="/volunteers?activity=active_this_month" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="mb-3 flex items-center justify-between"><span class="material-symbols-outlined rounded-lg bg-emerald-50 p-2 text-emerald-600">event_available</span><span class="text-xs font-semibold text-slate-400">This month</span></div>
            <p class="text-sm font-medium text-slate-500">Active This Month</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($activeThisMonth) }}</p>
        </a>
        <a href="/volunteers/applications?status=Pending" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="mb-3 flex items-center justify-between"><span class="material-symbols-outlined rounded-lg bg-amber-50 p-2 text-amber-600">pending_actions</span><span class="text-xs font-semibold text-slate-400">Needs review</span></div>
            <p class="text-sm font-medium text-slate-500">Pending Applications</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($pendingApprovals) }}</p>
        </a>
        <a href="/volunteers?status=Inactive" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="mb-3 flex items-center justify-between"><span class="material-symbols-outlined rounded-lg bg-slate-100 p-2 text-slate-600">person_off</span><span class="text-xs font-semibold text-slate-400">Account status</span></div>
            <p class="text-sm font-medium text-slate-500">Inactive Volunteers</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($inactiveVolunteers) }}</p>
        </a>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="/volunteers" class="grid grid-cols-1 gap-3 border-b border-slate-200 bg-slate-50 p-4 md:grid-cols-4">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone or skill" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-100 md:col-span-2">
            <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                <option value="">All account statuses</option>
                <option value="Active" @selected(request('status') === 'Active')>Active</option>
                <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
            </select>
            <select name="assignment_status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                <option value="">All assignment statuses</option>
                <option value="Assigned" @selected(request('assignment_status') === 'Assigned')>Assigned to my campaigns</option>
                <option value="Available" @selected(request('assignment_status') === 'Available')>Not assigned to any active campaign</option>
            </select>
            @if(request('activity') === 'active_this_month')
                <input type="hidden" name="activity" value="active_this_month">
            @endif
            <div class="flex gap-2 md:col-span-4">
                <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Apply Filters</button>
                <a href="/volunteers" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Clear</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-white">
                    <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4">Volunteer</th>
                        <th class="px-5 py-4">Contact</th>
                        <th class="px-5 py-4">Skills</th>
                        <th class="px-5 py-4">Registered</th>
                        <th class="px-5 py-4">Account</th>
                        <th class="px-5 py-4">My Campaigns</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($volunteers as $volunteer)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-600">{{ strtoupper(substr($volunteer->name, 0, 1)) }}</div>
                                    <div><p class="font-semibold text-slate-800">{{ $volunteer->name }}</p></div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600"><p>{{ $volunteer->email }}</p><p class="text-xs text-slate-400">{{ $volunteer->phone ?: 'No phone number' }}</p></td>
                            <td class="px-5 py-4">
                                <div class="flex max-w-xs flex-wrap gap-1">
                                    @forelse($volunteer->volunteerSkills as $volunteerSkill)
                                        @if($volunteerSkill->skill)<span class="rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">{{ $volunteerSkill->skill->name }}</span>@endif
                                    @empty
                                        <span class="text-sm text-slate-400">Not assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">{{ optional($volunteer->created_at)->format('d M Y') ?: '-' }}</td>
                            <td class="px-5 py-4">
                                @if(($volunteer->volunteer_status ?? 'Active') === 'Active')
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $volunteer->organizer_assignment_count }} <span class="text-slate-400">assigned</span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-3 text-sm">
                                    <a href="/volunteers/view/{{ $volunteer->id }}" class="font-semibold text-blue-600 hover:text-blue-800">View</a>
                                    <a href="/volunteers/edit/{{ $volunteer->id }}" class="font-semibold text-amber-600 hover:text-amber-800">Edit</a>
                                    <form method="POST" action="/volunteers/status/{{ $volunteer->id }}">
                                        @csrf
                                        <input type="hidden" name="volunteer_status" value="{{ ($volunteer->volunteer_status ?? 'Active') === 'Active' ? 'Inactive' : 'Active' }}">
                                        <button type="submit" class="font-semibold text-slate-600 hover:text-slate-900">{{ ($volunteer->volunteer_status ?? 'Active') === 'Active' ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                    @if($volunteer->volunteer_applications_count === 0 && $volunteer->campaign_assignments_count === 0 && $volunteer->attendances_count === 0)
                                        <form method="POST" action="/volunteers/delete/{{ $volunteer->id }}" onsubmit="return confirm('Delete this volunteer account? This cannot be undone.')">
                                            @csrf
                                            <button type="submit" class="font-semibold text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-500">No volunteers match your filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Showing {{ $volunteers->firstItem() ?? 0 }}–{{ $volunteers->lastItem() ?? 0 }} of {{ $volunteers->total() }} volunteers</p>
            {{ $volunteers->links() }}
        </div>
    </section>
</div>
@endsection
