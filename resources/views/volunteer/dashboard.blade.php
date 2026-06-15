@extends('layouts.volunteer')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="space-y-6">


        <!-- PAGE HEADER -->
        <div class="flex justify-between items-start">

            <div>
                <h1 class="text-5xl font-extrabold">
                    Welcome Back,
                    {{ strtoupper(session('user.name')) }}
                </h1>

                <p class="text-gray-500 mt-2">
                    Thank you for supporting community initiatives through volunteering.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('volunteer.history') }}"
                    class="bg-white border px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50">

                    View History

                </a>

            </div>

        </div>

        <!-- DASHBOARD CARDS -->

        <div class="grid md:grid-cols-4 gap-5">

            <a href="{{ route('volunteer.applications') }}"
                class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                <p class="text-gray-500 text-sm">Applications</p>
                <h2 class="text-3xl font-bold text-red-500">
                    {{ $totalApplications }}
                </h2>
            </a>

            <a href="{{ route('volunteer.applications') }}"
                class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                <p class="text-gray-500 text-sm">Approved</p>
                <h2 class="text-3xl font-bold text-green-500">
                    {{ $approved }}
                </h2>
            </a>

            <a href="{{ route('volunteer.applications') }}"
                class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                <p class="text-gray-500 text-sm">Pending</p>
                <h2 class="text-3xl font-bold text-yellow-500">
                    {{ $pending }}
                </h2>
            </a>

            <a href="{{ route('volunteer.applications') }}"
                class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                <p class="text-gray-500 text-sm">Volunteer Hours</p>
                <h2 class="text-3xl font-bold text-blue-500">
                    {{ $hours }}
                </h2>
            </a>

        </div>

        <!-- ACTIVE APPLICATIONS -->

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <div class="flex justify-between items-center p-5 border-b">

                <h2 class="text-lg font-semibold">
                    My Active Applications
                </h2>

                <span class="text-red-500 text-sm">
                    {{ $activeApplications->count() }} Active
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

                    @forelse($activeApplications as $app)

                        <tr onclick="window.location='{{ route('volunteer.application.view', $app->id) }}'"
                            class="border-t cursor-pointer hover:bg-gray-50">

                            <td class="p-4">
                                {{ $app->campaign->name ?? 'N/A' }}
                            </td>

                            <td class="p-4">
                                {{ $app->shift }}
                            </td>

                            <td class="p-4">

                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">

                                    {{ $app->status }}

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
                    Open Volunteer Campaigns
                </h2>

                <input type="text" placeholder="Search campaign..." class="border rounded-lg px-4 py-2 w-72">

            </div>

            <div class="grid md:grid-cols-3 gap-6">

                @foreach($campaigns as $campaign)

                        <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col">

                            <div class="h-40 bg-gray-200"></div>

                            <div class="p-5 flex flex-col flex-grow">

                                <span class="text-xs text-gray-500 uppercase">
                                    {{ $campaign->type }}
                                </span>

                                <h3 class="font-bold text-lg mt-2">
                                    {{ $campaign->name }}
                                </h3>

                                <p class="text-gray-600 text-sm mt-2 h-12 overflow-hidden">
                                    {{ Str::limit($campaign->description, 60) }}
                                </p>

                                <p class="text-gray-500 text-sm mt-3">
                                    {{ $campaign->location }}
                                </p>

                                <p class="text-gray-500 text-sm">
                                    {{ $campaign->start_date
                    ? \Carbon\Carbon::parse($campaign->start_date)->format('d M Y')
                    : 'N/A'
                                                                                                                                    }}
                                    -
                                    {{ $campaign->end_date
                    ? \Carbon\Carbon::parse($campaign->end_date)->format('d M Y')
                    : 'N/A'
                                                                                                                                    }}
                                </p>

                                <div class="mt-auto pt-4">

                                    <a href="{{ route('volunteer.application', $campaign->id) }}"
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