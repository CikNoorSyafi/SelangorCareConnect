@extends('layouts.organizer')

@section('content')

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center mb-6">

            <div>

                <h2 class="text-2xl font-bold">
                    Volunteer Applications
                </h2>

                <p class="text-gray-500">
                    Campaign:
                    <strong>{{ $campaign->name }}</strong>
                </p>

            </div>

            <a href="/campaign" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">

                Back

            </a>

        </div>

        @if(session('success'))

            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">

                {{ session('success') }}

            </div>

        @endif

        <table class="w-full border-collapse">

            <thead>

                <tr class="border-b bg-gray-50">

                    <th class="text-left p-3">Name</th>

                    <th class="text-left p-3">Email</th>

                    <th class="text-left p-3">Shift</th>

                    <th class="text-left p-3">Skill</th>

                    <th class="text-left p-3">Status</th>

                    <th class="text-left p-3">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($applications as $application)

                    <tr class="border-b">

                        <td class="p-3">
                            {{ $application->user->name }}
                        </td>

                        <td class="p-3">
                            {{ $application->user->email }}
                        </td>

                        <td class="p-3">
                            {{ $application->shift }}
                        </td>

                        <td class="p-3">
                            {{ $application->skill }}
                        </td>

                        <td class="p-3">

                            @if($application->status == 'Pending')

                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">
                                    Pending
                                </span>

                            @elseif($application->status == 'Approved')

                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                                    Approved
                                </span>

                            @elseif($application->status == 'Rejected')

                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                                    Rejected
                                </span>

                            @elseif($application->status == 'Withdrawn')

                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">
                                    Withdrawn
                                </span>

                            @endif

                        </td>

                        <td class="p-3">

                            @if($application->status == 'Pending')

                                <div class="flex gap-2">

                                    <form method="POST" action="{{ route('applications.approve', $application->id) }}">

                                        @csrf

                                        <select name="role_id" required class="border rounded px-2 py-1 text-sm">

                                            <option value="">
                                                Select Role
                                            </option>

                                            @foreach($roles as $role)

                                                <option value="{{ $role->id }}">

                                                    {{ $role->name }}

                                                </option>

                                            @endforeach

                                        </select>

                                        <select name="shift_id" required class="border rounded px-2 py-1 text-sm">

                                            <option value="">
                                                Select Shift
                                            </option>

                                            @foreach($shifts as $shift)

                                                <option value="{{ $shift->id }}">

                                                    {{ $shift->name }}

                                                </option>

                                            @endforeach

                                        </select>

                                        <button class="bg-green-500 text-white px-3 py-1 rounded text-sm">

                                            Approve

                                        </button>

                                    </form>

                                    <form method="POST" action="{{ route('applications.reject', $application->id) }}">

                                        @csrf

                                        <button class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">

                                            Reject

                                        </button>

                                    </form>

                                </div>

                            @elseif($application->status == 'Approved')

                                <span class="text-green-600 text-sm">
                                    Assigned
                                </span>

                            @elseif($application->status == 'Withdrawn')

                                <span class="text-gray-500 text-sm">
                                    Volunteer Left
                                </span>

                            @elseif($application->status == 'Rejected')

                                <span class="text-red-500 text-sm">
                                    Rejected
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center p-5 text-gray-500">

                            No applications found.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

@endsection