@extends('layouts.volunteer')

@section('content')

    <div class="space-y-6">

        <!-- PAGE HEADER -->
        <div class="flex justify-between items-center">

            <div>
                <h1 class="text-4xl font-bold">
                    Volunteer Dashboard
                </h1>

                <p class="text-gray-500 mt-2">
                    Browse campaigns and manage your volunteer participation.
                </p>
            </div>

            <a href="{{ route('volunteer.applications') }}"
                class="bg-white border px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50">

                View History

            </a>

        </div>

        <!-- DASHBOARD CARDS -->

        <div class="grid md:grid-cols-4 gap-5">

            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-gray-500 text-sm">Applications</p>
                <h2 class="text-3xl font-bold text-red-500">
                    {{ count(session('volunteer_applications', [])) }}
                </h2>
            </div>

            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-gray-500 text-sm">Approved</p>
                <h2 class="text-3xl font-bold text-green-500">
                    1
                </h2>
            </div>

            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-gray-500 text-sm">Pending</p>
                <h2 class="text-3xl font-bold text-yellow-500">
                    1
                </h2>
            </div>

            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-gray-500 text-sm">Volunteer Hours</p>
                <h2 class="text-3xl font-bold text-blue-500">
                    24
                </h2>
            </div>

        </div>

        <!-- ACTIVE APPLICATIONS -->

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <div class="flex justify-between items-center p-5 border-b">

                <h2 class="text-lg font-semibold">
                    My Active Applications
                </h2>

                <span class="text-red-500 text-sm">
                    {{ count(session('volunteer_applications', [])) }} Active
                </span>

            </div>

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="text-left p-4">Campaign</th>
                        <th class="text-left p-4">Shift</th>
                        <th class="text-left p-4">Status</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse(session('volunteer_applications', []) as $app)

                        <tr class="border-t">

                            <td class="p-4">
                                {{ $app['campaign'] }}
                            </td>

                            <td class="p-4">
                                {{ $app['shift'] }}
                            </td>

                            <td class="p-4">

                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">

                                    {{ $app['status'] }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="3" class="text-center p-6 text-gray-500">
                                No active applications.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <!-- AVAILABLE CAMPAIGNS -->

        <div>

            <div class="flex justify-between items-center mb-4">

                <h2 class="text-xl font-bold">
                    Available Campaigns
                </h2>

                <input type="text" placeholder="Search campaign..." class="border rounded-lg px-4 py-2 w-72">

            </div>

            <div class="grid md:grid-cols-3 gap-6">

                @foreach($campaigns as $campaign)

                    <div class="bg-white rounded-xl shadow overflow-hidden">

                        <div class="h-40 bg-gray-200"></div>

                        <div class="p-5">

                            <span class="text-xs text-gray-500 uppercase">

                                {{ $campaign['category'] }}

                            </span>

                            <h3 class="font-bold text-lg mt-2">

                                {{ $campaign['title'] }}

                            </h3>

                            <p class="text-gray-500 text-sm mt-2">

                                {{ $campaign['location'] }}

                            </p>

                            <p class="text-gray-500 text-sm">

                                {{ $campaign['date'] }}

                            </p>

                            <div class="mt-4">

                                <a href="{{ route('volunteer.application', $campaign['id']) }}"
                                    class="block text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg">

                                    Apply Now

                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

@endsection