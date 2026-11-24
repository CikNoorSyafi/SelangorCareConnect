@extends('layouts.organizer')

@section('content')

    <div class="flex justify-between items-center mb-6">

    <h1 class="text-2xl font-bold">
        Campaign Management
    </h1>

    <a href="/campaign/create"
       class="bg-red-500 text-white px-4 py-2 rounded">
        + Create Campaign
    </a>

</div>

    <p class="text-gray-500 mb-6">
        Create new volunteer initiatives, manage existing events, and track your impact.
    </p>

    <!-- STATS -->
    <div class="grid grid-cols-4 gap-4 mb-6">

       <a href="/campaign"
   class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition block">

    <p class="text-gray-500">
        Total Campaigns
    </p>

    <h2 class="text-4xl font-bold">
        {{ $totalCampaigns }}
    </h2>

</a>

        <a href="/campaign?status=active"
   class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition block">

    <p class="text-gray-500">
        Active Now
    </p>

    <h2 class="text-4xl font-bold">
        {{ $activeCampaigns }}
    </h2>

</a>

        <a href="/campaign?status=fundraising"
   class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition block">

    <p class="text-gray-500">
        Pending
    </p>

    <h2 class="text-4xl font-bold">
        {{ $pendingCampaigns }}
    </h2>

</a>

        <a href="/campaign?status=completed"
   class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition block">

    <p class="text-gray-500">
        Completed
    </p>

    <h2 class="text-4xl font-bold">
        {{ $completedCampaigns }}
    </h2>

</a>

    </div>


    @include('organizer.partials.campaign-map-overview', [
        'mapId' => 'organizer-campaign-map',
        'mapTitle' => 'Selangor Campaign Event Map',
        'mapDescription' => 'Explore your programmes across all nine Selangor administrative districts. Hover over a pin to see the event details.',
        'campaignMapPoints' => $campaignMapPoints,
        'campaignListUrl' => url('/campaign'),
        'selectedMapCampaignId' => $selectedMapCampaignId ?? null,
        'selectedDistrict' => $districtFilter ?? null,
    ])

    <!-- EXISTING CAMPAIGNS -->

    <div class="mt-10" id="existing-campaigns">

        @if($campaigns->count())

            <div class="bg-white p-6 rounded shadow">

                <div class="flex justify-between items-center mb-6">

                    <div>
                        <h2 class="text-2xl font-bold">
                            Existing Campaigns
                        </h2>
                        @if(!empty($selectedMapCampaignId))
                            <p class="mt-1 text-sm font-medium text-teal-700">
                                Showing the campaign selected from the map.
                                <a href="{{ url('/campaign') }}#existing-campaigns" class="ml-1 underline">Show all campaigns</a>
                            </p>
                        @elseif(!empty($districtFilter))
                            <p class="mt-1 text-sm font-medium text-teal-700">
                                Showing campaigns in {{ $districtFilter }} District.
                                <a href="{{ url('/campaign') }}#existing-campaigns" class="ml-1 underline">Clear district filter</a>
                            </p>
                        @endif
                    </div>

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


                    </div>

                </div>

                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left py-3">
                                Campaign
                            </th>

                            <th class="text-left py-3">
                                District
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

                            <tr id="campaign-row-{{ $campaign->id }}" class="{{ !$loop->last ? 'border-b' : '' }} campaign-row {{ (int) ($selectedMapCampaignId ?? 0) === (int) $campaign->id ? 'bg-teal-50 ring-2 ring-inset ring-teal-500' : '' }}">

                                <td class="py-3 font-semibold">
                                    {{ $campaign->name }}
                                </td>

                                <td class="text-sm text-gray-600">
                                    {{ \App\Support\SelangorDistricts::resolve($campaign->location) ?? ($campaign->location ?: '—') }}
                                </td>

                                <td>
                                    RM {{ number_format((float) $campaign->target, 2) }}
                                </td>

                                <td>
                                    {{ $campaign->volunteers_count }}
                                </td>

                                <td>

    @if($campaign->status == 'Fundraising')

        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">
            Fundraising
        </span>

    @elseif($campaign->status == 'Recruiting')

        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">
            Recruiting
        </span>

    @elseif($campaign->status == 'In Progress')

        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
            In Progress
        </span>

    @elseif($campaign->status == 'Completed')

        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs">
            Completed
        </span>

    @elseif($campaign->status == 'Closed')

        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">
            Closed
        </span>

    @else

        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">
            {{ $campaign->status }}
        </span>

    @endif

</td>

                             <td>

<div class="flex flex-wrap gap-2">

@if($campaign->status == 'Fundraising')

    <a href="/campaign/recruiting/{{ $campaign->id }}"
       class="bg-indigo-500 text-white px-3 py-1 rounded text-sm">

        Start Recruitment

    </a>

@endif

@if($campaign->status == 'Recruiting')

    <a href="/campaign/start/{{ $campaign->id }}"
       class="bg-green-500 text-white px-3 py-1 rounded text-sm">

        Start Campaign

    </a>

@endif

@if($campaign->status == 'In Progress')

    <a href="/campaign/attendance/{{ $campaign->id }}"
       class="bg-orange-500 text-white px-3 py-1 rounded text-sm hover:bg-orange-600">

        Attendance

    </a>

    <a href="/campaign/complete/{{ $campaign->id }}"
       class="bg-purple-500 text-white px-3 py-1 rounded text-sm hover:bg-purple-600">

        Complete

    </a>

@endif

<a href="/campaign/edit/{{ $campaign->id }}"
   class="bg-blue-500 text-white px-3 py-1 rounded text-sm">

    Edit

</a>

<a href="/organizer/campaigns/{{ $campaign->id }}/applications"
   class="bg-cyan-500 text-white px-3 py-1 rounded text-sm hover:bg-cyan-600">

    Applications

</a>

<form action="/campaign/delete/{{ $campaign->id }}"
      method="POST"
      class="inline"
      onsubmit="return confirm('Campaign: {{ $campaign->name }} will be permanently deleted.\n\nAre you sure you want to proceed?')">

    @csrf

    <button type="submit"
            class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">

        Delete

    </button>

</form>

</div>

</td>

                            </tr>

                            <tr id="campaign-details-row-{{ $campaign->id }}"
                                class="campaign-details-row border-b border-teal-100 bg-teal-50/60 {{ (int) ($selectedMapCampaignId ?? 0) === (int) $campaign->id ? '' : 'hidden' }}">
                                <td colspan="6" class="px-4 pb-5 pt-2">
                                    <div class="rounded-lg border border-teal-100 bg-white p-4">
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wider text-teal-700">{{ \App\Support\SelangorDistricts::resolve($campaign->location) ?? $campaign->location }} District · Selected from map</p>
                                                <h3 class="mt-1 text-lg font-bold text-slate-900">{{ $campaign->name }}</h3>
                                            </div>
                                            <div class="text-sm text-slate-600">
                                                <span class="font-semibold">Programme dates:</span>
                                                {{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('j M Y') : '—' }}
                                                –
                                                {{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('j M Y') : '—' }}
                                            </div>
                                        </div>
                                        <p class="mt-3 max-h-40 overflow-y-auto whitespace-pre-wrap break-all text-sm leading-6 text-slate-600">{{ $campaign->description ?: 'No campaign description has been provided.' }}</p>
                                    </div>
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

        @else
            <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
                <p class="font-semibold text-slate-700">No campaigns found for this selection.</p>
                <p class="mt-1 text-sm text-slate-500">Choose another district or show all campaigns.</p>
                <a href="{{ url('/campaign') }}#existing-campaigns" class="mt-3 inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Show all campaigns</a>
            </div>
        @endif

    </div>




@endsection