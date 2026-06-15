@extends('layouts.volunteer')

@section('content')

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center mb-8">

            <div>
                <h1 class="text-5xl font-extrabold">
                    My Assignments
                </h1>

                <p class="text-gray-500 mt-2">
                    View your approved volunteer assignments.
                </p>
            </div>

            <a href="{{ route('volunteer.dashboard') }}"
                class="px-8 py-4 border rounded-3xl bg-white hover:bg-gray-50 shadow-sm">

                Back to Dashboard

            </a>

        </div>

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>
                    <th class="p-4 text-left">Campaign</th>
                    <th class="p-4 text-left">Date</th>
                    <th class="p-4 text-left">Shift</th>
                    <th class="p-4 text-left">Skill</th>
                    <th class="p-4 text-left">Status</th>
                </tr>

            </thead>

            <tbody>

                @forelse($assignments as $assignment)

                    <tr class="border-t">

                        <td class="p-4">
                            {{ $assignment->campaign->name ?? 'N/A' }}
                        </td>

                        <td class="p-4">
                            {{ \Carbon\Carbon::parse($assignment->campaign->start_date)->format('d M Y') }}
                        </td>

                        <td class="p-4">
                            {{ $assignment->shift }}
                        </td>

                        <td class="p-4">
                            {{ $assignment->skill }}
                        </td>

                        <td class="p-4">

                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">

                                Assigned

                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center p-8 text-gray-500">

                            No assignments available.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

@endsection