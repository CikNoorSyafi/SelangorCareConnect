@extends('layouts.volunteer')

@section('content')

    <h1 class="text-3xl font-bold mb-6">
        Attendance Tracking
    </h1>

    <div class="grid grid-cols-3 gap-5 mb-6">

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">
                Total Volunteer Hours
            </p>

            <h2 class="text-3xl font-bold text-red-500">
                12
            </h2>

        </div>

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">
                Campaigns Attended
            </p>

            <h2 class="text-3xl font-bold">
                2
            </h2>

        </div>

        <div class="bg-white rounded-xl shadow p-5">

            <p class="text-gray-500 text-sm">
                Attendance Rate
            </p>

            <h2 class="text-3xl font-bold text-green-500">
                100%
            </h2>

        </div>

    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>
                    <th class="p-4 text-left">Campaign</th>
                    <th class="p-4 text-left">Date</th>
                    <th class="p-4 text-left">Check In</th>
                    <th class="p-4 text-left">Check Out</th>
                    <th class="p-4 text-left">Hours</th>
                </tr>

            </thead>

            <tbody>

                @foreach($attendance as $record)

                    <tr class="border-t">

                        <td class="p-4">
                            {{ $record['campaign'] }}
                        </td>

                        <td class="p-4">
                            {{ $record['date'] }}
                        </td>

                        <td class="p-4">
                            {{ $record['checkin'] }}
                        </td>

                        <td class="p-4">
                            {{ $record['checkout'] }}
                        </td>

                        <td class="p-4">
                            {{ $record['hours'] }} hrs
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

@endsection