@extends('layouts.organizer')

@section('content')

    <h1 class="text-2xl font-bold mb-4">Volunteer Management</h1>

    <!-- DASHBOARD STATS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <!-- TOTAL VOLUNTEERS -->
        <a href="/volunteers"
            class="bg-white border border-red-100 rounded-xl p-5 shadow-sm hover:shadow-md transition block">

            <div class="flex justify-between items-start mb-4">

                <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-500">
                        groups
                    </span>
                </div>

                <span class="text-green-600 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">
                    +4.5%
                </span>

            </div>

            <p class="text-xs uppercase tracking-wide text-gray-500 font-bold mb-2">
                Total Volunteers
            </p>

            <h2 class="text-5xl font-black text-gray-900 leading-none">
                {{ $totalVolunteers }}
            </h2>

        </a>

        <!-- ACTIVE -->
        <a href="/volunteers?status=Active"
            class="bg-white border border-red-100 rounded-xl p-5 shadow-sm hover:shadow-md transition block">

            <div class="flex justify-between items-start mb-4">

                <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600">
                        bolt
                    </span>
                </div>

                <span class="text-green-600 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">
                    +12%
                </span>

            </div>

            <p class="text-xs uppercase tracking-wide text-gray-500 font-bold mb-2">
                Active This Month
            </p>

            <h2 class="text-5xl font-black text-gray-900 leading-none">
                {{ $activeThisMonth }}
            </h2>

        </a>

        <!-- PENDING -->
        <a href="/volunteers?status=Pending"
            class="bg-white border border-red-100 rounded-xl p-5 shadow-sm hover:shadow-md transition block">

            <div class="flex justify-between items-start mb-4">

                <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-500">
                        pending_actions
                    </span>
                </div>

                <span class="text-red-600 text-xs font-bold bg-red-100 px-2 py-1 rounded-full">
                    Urgent
                </span>

            </div>

            <p class="text-xs uppercase tracking-wide text-gray-500 font-bold mb-2">
                Pending Approvals
            </p>

            <h2 class="text-5xl font-black text-gray-900 leading-none">
                {{ $pendingApprovals }}
            </h2>

        </a>

        <!-- INACTIVE -->
        <a href="/volunteers?status=Inactive"
            class="bg-white border border-red-100 rounded-xl p-5 shadow-sm hover:shadow-md transition block">

            <div class="flex justify-between items-start mb-4">

                <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600">
                        star
                    </span>
                </div>

                <span class="text-gray-600 text-xs font-bold">
                    Top 5%
                </span>

            </div>

            <p class="text-xs uppercase tracking-wide text-gray-500 font-bold mb-2">
                Inactive Volunteers
            </p>

            <h2 class="text-5xl font-black text-gray-900 leading-none">
                {{ $inactiveVolunteers }}
            </h2>
        </a>

    </div>

    <a href="/volunteers/create" class="bg-red-500 text-white px-4 py-2 rounded mb-4 inline-block">
        + Register New Volunteer
    </a>

    @if(session('success'))
        <p class="text-green-500 mb-3">{{ session('success') }}</p>
    @endif

    <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">


        <!-- FILTER BAR -->
        <form method="GET" action="/volunteers" class="flex justify-between items-center p-5 border-b bg-red-50/30">

            <div class="flex gap-3 items-center w-full flex-wrap">

                <!-- SEARCH -->
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Filter by name, skill, or status..."
                    class="border border-gray-200 rounded-lg px-4 py-3 w-96 focus:outline-none focus:ring-2 focus:ring-red-300">


                <!-- FILTER BUTTON -->
                <button type="submit" class="bg-red-500 text-white px-5 py-3 rounded-lg hover:bg-red-600 transition">

                    Filter

                </button>

                <!-- CLEAR FILTER -->
                <a href="/volunteers" class="bg-gray-200 text-gray-700 px-5 py-3 rounded-lg hover:bg-gray-300 transition">

                    Clear Filter

                </a>

            </div>

        </form>

        <!-- TABLE -->
        <table class="w-full">

            <thead class="bg-red-50 text-gray-700 text-sm uppercase">

                <tr>
                    <th class="text-left px-8 py-4">Volunteer Name</th>
                    <th class="text-left px-6 py-4">Email</th>
                    <th class="text-left px-6 py-4">Skills</th>
                    <th class="text-left px-6 py-4">Registered Date</th>
                    <th class="text-left px-6 py-4">Status</th>
                    <th class="text-center px-6 py-4">Actions</th>
                </tr>

            </thead>

            <tbody class="divide-y">

                @foreach($volunteers as $v)

                        <tr class="hover:bg-gray-50 transition">

                            <!-- NAME -->
                            <td class="px-8 py-5">

                                <div class="flex items-center gap-4">

                                    <!-- AVATAR -->
                                    <div
                                        class="w-14 h-14 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center text-xl font-bold">
                                        {{ strtoupper(substr($v->name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <p class="font-semibold text-lg text-gray-800">
                                            {{ $v->name }}
                                        </p>

                                        <p class="text-gray-400 text-sm">
                                            ID: SCC-00{{ $v->id }}
                                        </p>
                                    </div>

                                </div>

                            </td>

                            <!-- EMAIL -->
                            <td class="px-6 py-5 text-gray-700">
                                {{ $v->email }}
                            </td>

                            <!-- SKILLS -->
                            <td class="px-6 py-5">

                                <div class="flex flex-wrap gap-2">

                                    @forelse($v->volunteerSkills as $vs)

                                        <span class="bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs">

                                            {{ $vs->skill->name }}

                                        </span>

                                    @empty

                                        <span class="text-gray-400">

                                            Not Assigned

                                        </span>

                                    @endforelse

                                </div>

                            </td>

                            <!-- REGISTERED DATE -->
                            <td class="px-6 py-5 text-gray-600">

                                {{ $v->created_at
                    ? $v->created_at->format('d M Y')
                    : '-' }}

                            </td>

                            <!-- STATUS -->
                            <td class="px-6 py-5">

                                @php

                                    $assignmentCount =
                                        \App\Models\CampaignVolunteer::where(
                                            'volunteer_id',
                                            $v->id
                                        )->count();

                                @endphp

                                @if($assignmentCount > 0)

                                    <span class="px-4 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">

                                        Assigned

                                    </span>

                                @else

                                    <span class="px-4 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-600">

                                        Available

                                    </span>

                                @endif

                            </td>


                            <!-- ACTIONS -->
                            <td class="px-6 py-5">

                                <div class="flex justify-center gap-5">

                                    <!-- VIEW -->
                                    <a href="/volunteers/view/{{ $v->id }}" class="text-gray-400 hover:text-blue-500 transition">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                                                                                                                                                                                                                                                                                                                     c4.478 0 8.268 2.943 9.542 7
                                                                                                                                                                                                                                                                                                                                                     -1.274 4.057-5.064 7-9.542 7
                                                                                                                                                                                                                                                                                                                                                     -4.477 0-8.268-2.943-9.542-7z" />

                                        </svg>

                                    </a>

                                    <!-- EDIT -->
                                    <a href="/volunteers/edit/{{ $v->id }}" class="text-gray-400 hover:text-yellow-500 transition">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.586-9.414
                                                                                                                                                                                                                                                                                                                         a2 2 0 112.828 2.828L11.828 17H9v-2.828
                                                                                                                                                                                                                                                                                                                         l8.414-8.586z" />

                                        </svg>

                                    </a>

                                    <!-- DELETE -->
                                    <a href="/volunteers/delete/{{ $v->id }}" class="text-gray-400 hover:text-red-500 transition">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                                                                                                                                                                                                                                                                                                                     a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                                                                                                                                                                                                                                                                                                                                     M1 7h22M9 3h6a1 1 0 011 1v2H8V4a1 1 0 011-1z" />

                                        </svg>

                                    </a>

                                </div>

                            </td>

                        </tr>

                @endforeach

            </tbody>

        </table>

        <!-- FOOTER -->
        <div class="flex justify-between items-center px-8 py-5 border-t">

            <p class="text-gray-500 text-sm">

                Showing

                {{ $volunteers->firstItem() ?? 0 }}

                -

                {{ $volunteers->lastItem() ?? 0 }}

                of

                {{ $volunteers->total() }}

                volunteers

            </p>

            <div class="flex gap-2">

                @for ($i = 1; $i <= $volunteers->lastPage(); $i++)

                        <a href="{{ $volunteers->url($i) }}" class="w-10 h-10 flex items-center justify-center rounded border
                                                    {{ $volunteers->currentPage() == $i
                    ? 'bg-red-500 text-white border-red-500'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100' }}">

                            {{ $i }}

                        </a>

                @endfor

            </div>

        </div>>

    </div>

@endsection