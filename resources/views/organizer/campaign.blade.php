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
            <h2 class="text-xl font-bold">{{ $totalCampaigns }}</h2>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Active Now</p>
            <h2 class="text-xl font-bold">{{ $activeCampaigns }}</h2>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-xl font-bold">{{ $pendingCampaigns }}</h2>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-500">Completed</p>
            <h2 class="text-xl font-bold">{{ $completedCampaigns }}</h2>
        </div>

    </div>




    <!-- FORM Create New Campaign-->

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

                    <input name="campaign_name" required type="text" placeholder="Enter campaign name"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

                <!-- CAMPAIGN TYPE -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Campaign Type
                    </label>

                    <select name="campaign_type" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                        <option value="" selected disabled>
                            Select Campaign Type
                        </option>

                        @foreach($campaignTypes as $type)

                            <option value="{{ $type->name }}">
                                {{ $type->name }}
                            </option>

                        @endforeach

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

                    <input type="text" name="location" required list="selangor-locations" placeholder="Type location..."
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">
                    <datalist id="selangor-locations">

                        @foreach($locations as $location)

                            <option value="{{ $location }}">

                        @endforeach

                    </datalist>

                </div>

                <!-- FUNDING TARGET -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Funding Target (RM)
                    </label>

                    <input name="funding_target" required type="text" id="fundingTarget" placeholder="0.00" maxlength="13"
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


            <!-- ROLES -->
            <div class="mt-4">

                <label class="block text-sm font-semibold mb-2">
                    Required Skills
                </label>

                <select name="required_skills[]" multiple class="w-full border rounded p-2">

                    @foreach($skills as $skill)

                        <option value="{{ $skill->id }}">
                            {{ $skill->name }}
                        </option>

                    @endforeach

                </select>

                <p class="text-xs text-gray-500 mt-1">
                    Hold CTRL to select multiple skills.
                </p>

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

                        </tr>
                    </thead>

                    <tbody>

                        @foreach($volunteers as $v)

                            <tr>

                                <td class="p-2">

                                    <label class="flex items-start gap-2">

                                        <input type="checkbox" name="volunteers[]" value="{{ $v->id }}" class="mt-1">

                                        <div>

                                            {{ $v->name }}

                                            <br>

                                            <span class="text-xs text-gray-400">
                                                ID: {{ $v->id }}
                                            </span>

                                        </div>

                                    </label>

                                </td>

                                <td class="p-2">

                                    <select name="role_id[{{ $v->id }}]" class="border p-1 rounded">

                                        @foreach($roles as $role)

                                            <option value="{{ $role->id }}">
                                                {{ $role->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </td>

                                <td class="p-2">

                                    <select name="shift_id[{{ $v->id }}]" class="border p-1 rounded">

                                        @foreach($shifts as $shift)

                                            <option value="{{ $shift->id }}">
                                                {{ $shift->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </td>

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
        </div>

    </form>

    <!-- EXISTING CAMPAIGNS -->

    <div class="mt-10">

        @if($campaigns->count())

            <div class="bg-white p-6 rounded shadow">

                <div class="flex justify-between items-center mb-6">

                    <h2 class="text-2xl font-bold">
                        Existing Campaigns
                    </h2>

                    <div class="relative w-80">

                        <form method="GET" action="{{ url('/campaign') }}">

                            <div class="relative">

                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                                    class="w-full border border-gray-300 rounded-md pl-4 pr-12 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">

                                <button type="submit"
                                    class="absolute inset-y-0 right-0 flex items-center px-4 bg-gray-100 border-l border-gray-300 rounded-r-md">

                                    <span class="material-symbols-outlined text-gray-700">
                                        search
                                    </span>

                                </button>

                            </div>

                        </form>

                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 bg-gray-100 border-l border-gray-300 rounded-r-md">

                            <span class="material-symbols-outlined text-gray-700">
                                search
                            </span>

                        </div>

                    </div>

                </div>

                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left py-3">
                                Campaign
                            </th>

                            <th class="text-left py-3">
                                Target
                            </th>

                            <th class="text-left py-3">
                                Volunteers
                            </th>

                            <th class="text-left py-3">
                                Status
                            </th>

                            <th class="text-left py-3">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($campaigns as $campaign)

                            <tr class="{{ !$loop->last ? 'border-b' : '' }} campaign-row">

                                <td class="py-3">
                                    {{ $campaign->name }}
                                </td>

                                <td>
                                    RM {{ number_format((float) $campaign->target, 2) }}
                                </td>

                                <td>
                                    {{ $campaign->volunteers_count }}
                                </td>

                                <td>
                                    {{ $campaign->status }}
                                </td>

                                <td class="space-x-2">

                                    <a href="/campaign/edit/{{ $campaign->id }}"
                                        class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
                                        Edit
                                    </a>

                                    <form action="/campaign/delete/{{ $campaign->id }}" method="POST" class="inline"
                                        onsubmit="return confirm('Campaign: {{ $campaign->name }} will be permanently deleted.\n\nAre you sure you want to proceed?')">

                                        @csrf

                                        <button type="submit"
                                            class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">

                                            Delete

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

                <!-- PAGINATION -->

                <div class="flex justify-between items-center mt-6 border-t pt-4">

                    <div class="text-sm text-gray-500">

                        Showing
                        {{ $campaigns->firstItem() ?? 0 }}
                        -
                        {{ $campaigns->lastItem() ?? 0 }}
                        of
                        {{ $campaigns->total() }}
                        campaigns

                    </div>

                    <div class="flex gap-2">

                        @for($i = 1; $i <= $campaigns->lastPage(); $i++)

                                <a href="{{ $campaigns->url($i) }}" class="px-3 py-1 border rounded
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               {{ $campaigns->currentPage() == $i
                            ? 'bg-red-500 text-white'
                            : 'bg-white' }}">

                                    {{ $i }}

                                </a>

                        @endfor

                    </div>

                </div>
            </div>

        @endif

    </div>


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

        document.querySelector('form')
            .addEventListener(
                'keydown',
                function (e) {

                    if (e.key === 'Enter') {

                        e.preventDefault();

                    }

                }
            );

    </script>

@endsection