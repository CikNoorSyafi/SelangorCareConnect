@extends('layouts.volunteer')

@section('content')

    <h1 class="text-3xl font-bold mb-6">
        Volunteer History
    </h1>

    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>
                    <th class="p-4 text-left">Application ID</th>
                    <th class="p-4 text-left">Campaign</th>
                    <th class="p-4 text-left">Shift</th>
                    <th class="p-4 text-left">Status</th>
                    <th class="p-4 text-left">Submitted Date</th>
                </tr>

            </thead>

            <tbody>

                @forelse($applications as $application)

                    <tr class="border-t">

                        <td class="p-4">
                            {{ $application['id'] }}
                        </td>

                        <td class="p-4">
                            {{ $application['campaign'] }}
                        </td>

                        <td class="p-4">
                            {{ $application['shift'] }}
                        </td>

                        <td class="p-4">

                            @if($application['status'] == 'Withdrawn')

                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm">
                                    Withdrawn
                                </span>

                            @else

                                <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-sm">
                                    {{ $application['status'] }}
                                </span>

                            @endif

                        </td>

                        <td class="p-4">
                            {{ $application['date'] }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="p-6 text-center text-gray-500">

                            No volunteer history available.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

@endsection