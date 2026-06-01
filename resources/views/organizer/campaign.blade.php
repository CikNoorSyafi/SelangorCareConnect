@extends('layouts.organizer')

@section('content')



    <h1 class="text-2xl font-bold mb-2">Campaign Management</h1>
    @if(session('success'))

        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">

            {{ session('success') }}

        </div>

    @endif

    <p class="text-gray-500 mb-6">
        Create new volunteer initiatives, manage existing events, and track your impact.
    </p>

    <!-- STATS -->
    <div class="grid grid-cols-4 gap-4 mb-6">

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Total Campaigns</p>
            <h2 class="text-xl font-bold">24</h2>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Active Now</p>
            <h2 class="text-xl font-bold">8</h2>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-xl font-bold">2</h2>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Completed</p>
            <h2 class="text-xl font-bold">14</h2>
        </div>

    </div>

    <!-- FILTER -->
    <div class="flex justify-between items-center mb-4">

        <h2 class="text-lg font-semibold">Existing Campaigns</h2>

        <div class="flex gap-2">
            <input type="text" placeholder="Search campaigns..." class="border px-3 py-2 rounded text-sm">

            <button class="border px-3 py-2 rounded text-sm">Filter</button>
            <button class="border px-3 py-2 rounded text-sm">Export</button>
        </div>

    </div>


    <!-- FORM -->
    <form method="POST" action="/campaign/store">

        @csrf

        <div class="bg-white p-6 rounded shadow">

            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-red-500">Create New Campaign</h2>
                <button class="text-sm text-gray-500 underline">Clear Form</button>
            </div>

            <!-- ROW 1 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <!-- CAMPAIGN NAME -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Campaign Name
                    </label>

                    <input name="campaign_name" type="text" placeholder="Enter campaign name"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

                <!-- CAMPAIGN TYPE -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Campaign Type
                    </label>

                    <select name="campaign_type" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                        <option selected disabled>
                            Select Campaign Type
                        </option>

                        <option>
                            Disaster Relief
                        </option>

                        <option>
                            Education
                        </option>

                        <option>
                            Medical Support
                        </option>

                        <option>
                            Welfare Management
                        </option>

                        <option>
                            Community Development
                        </option>

                    </select>

                </div>

            </div>


            <!-- ROW 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <!-- LOCATION -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Location
                    </label>

                    <input name="location" type="text" placeholder="Enter campaign location"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

                <!-- FUNDING TARGET -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Funding Target (RM)
                    </label>

                    <input name="funding_target" type="text" id="fundingTarget" placeholder="0.00" maxlength="13"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

            </div>

            <!-- ROW 3 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                <!-- START DATE -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Start Date
                    </label>

                    <input name="start_date" type="date" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

                <!-- END DATE -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        End Date
                    </label>

                    <input name="end_date" type="date" class=" w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

            </div>
        </div>

        <!-- ROLES -->
        <div class="mt-4">
            <p class="text-sm font-semibold mb-2">Volunteer Roles Needed</p>

            <div class="flex gap-2 flex-wrap">
                <span class="bg-red-100 text-red-500 px-3 py-1 rounded-full text-xs">
                    General Helper ✕
                </span>
                <span class="bg-red-100 text-red-500 px-3 py-1 rounded-full text-xs">
                    First Aid ✕
                </span>
                <button class="bg-gray-100 px-3 py-1 rounded-full text-xs">
                    + Add Role
                </button>
            </div>
        </div>

        <!-- ASSIGN VOLUNTEERS -->
        <div class="mt-6 border p-4 rounded">

            <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold">Assign Volunteers</h3>
                <span class="text-xs text-gray-400">Optional</span>
            </div>

            <div class="flex gap-2 mb-3">
                <input type="text" placeholder="Search volunteers..." class="border p-2 rounded w-full text-sm">

                <button class="border px-3 rounded text-sm">Search</button>
            </div>

            <table class="w-full text-sm border">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Volunteer</th>
                        <th class="p-2 text-left">Role</th>
                        <th class="p-2 text-left">Shift</th>
                        <th class="p-2 text-center">Remove</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($volunteers as $v)
                        <tr>

                            <td class="p-2">

                                <label class="flex items-start gap-2">

                                    <input type="checkbox" name="volunteers[]" value="{{ $v['id'] }}" class="mt-1">

                                    <div>
                                        {{ $v['name'] }} <br>

                                        <span class="text-xs text-gray-400">
                                            ID: {{ $v['id'] }}
                                        </span>
                                    </div>

                                </label>

                            </td>

                            <td class="p-2">
                                <select class="border p-1 rounded">
                                    <option>General Helper</option>
                                    <option>First Aid</option>
                                </select>
                            </td>

                            <td class="p-2">
                                <select class="border p-1 rounded">
                                    <option>Full Event</option>
                                    <option>Morning Shift</option>
                                </select>
                            </td>

                            <td class="p-2 text-center">✕</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

        <textarea name="description" class="border p-3 rounded w-full mt-4" placeholder="Description"></textarea>

        <div class="flex justify-end mt-4 gap-2">
            <button class="px-4 py-2 border rounded">Cancel</button>
            <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded">
                Save Campaign
            </button>
        </div>

    </form>

    @if(isset($campaigns) && count($campaigns) > 0)

        <div class="bg-white rounded-xl p-6 mt-8">

            <h2 class="text-xl font-bold mb-4">
                Existing Campaigns
            </h2>

            <table class="w-full">

                <thead>

                    <tr class="border-b">

                        <th class="text-left py-2">
                            Campaign
                        </th>

                        <th class="text-left py-2">
                            Target
                        </th>

                        <th class="text-left py-2">
                            Volunteers
                        </th>

                        <th class="text-left py-2">
                            Status
                        </th>

                        <th class="text-left py-2">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($campaigns as $campaign)

                        <tr class="border-b">

                            <td class="py-3">
                                {{ $campaign['name'] }}
                            </td>

                            <td>
                                RM {{ number_format((float) str_replace(',', '', $campaign['target']), 2) }}
                            </td>

                            <td>
                                {{ count($campaign['volunteers'] ?? []) }}
                            </td>

                            <td>
                                {{ $campaign['status'] }}
                            </td>
                            <td class="space-x-2">

                                <a href="/campaign/edit/{{ $campaign['id'] }}"
                                    class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
                                    Edit
                                </a>

                                <form action="/campaign/delete/{{ $campaign['id'] }}" method="POST" style="display:inline;">
                                    @csrf

                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif


    <script>

        const fundingInput = document.getElementById('fundingTarget');

        // Allow only numbers and decimal
        fundingInput.addEventListener('input', function (e) {

            let value = e.target.value;

            // Remove invalid chars
            value = value.replace(/[^0-9.]/g, '');

            // Prevent multiple decimals
            const parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts[1];
            }

            // Limit integer part to 10 digits
            if (parts[0].length > 10) {
                parts[0] = parts[0].substring(0, 10);
                value = parts.join('.');
            }

            // Limit decimal places
            if (parts[1]) {
                parts[1] = parts[1].substring(0, 2);
                value = parts[0] + '.' + parts[1];
            }

            e.target.value = value;
        });

        // Format nicely on blur
        fundingInput.addEventListener('blur', function (e) {

            let value = parseFloat(e.target.value);

            if (!isNaN(value)) {

                e.target.value = value.toLocaleString('en-MY', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

            }
        });

    </script>
@endsection