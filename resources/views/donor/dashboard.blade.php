@extends('layouts.donor')

@section('content')

    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-8">

            <div>
                <h1 class="text-5xl font-black">
                    Welcome Back, {{ session('user.name') }}
                </h1>

                <p class="text-gray-500 mt-2">
                    Thank you for supporting community initiatives.
                </p>
            </div>

            <a href="/donor/fund" class="bg-red-500 text-white px-6 py-3 rounded-xl">
                Contribute to Community Fund
            </a>

        </div>

        <!-- Statistics -->

        <div class="grid md:grid-cols-3 gap-6 mb-8">

            <a href="/donor/history">

                <div class="bg-white border rounded-3xl p-6 hover:shadow-lg transition cursor-pointer">

                    <p class="text-gray-400">
                        Total Contributed
                    </p>

                    <h2 class="text-4xl font-bold text-red-500">

                        RM {{ number_format($totalContributed, 2) }}

                    </h2>

                </div>

            </a>

            <a href="/donor/history">

                <div class="bg-white border rounded-3xl p-6 hover:shadow-lg transition cursor-pointer">

                    <p class="text-gray-400">
                        Campaigns Supported
                    </p>

                    <h3 class="text-4xl font-black mt-2">
                        {{ $campaignsSupported }}
                    </h3>

                </div>

            </a>

            <a href="/donor/fund">

                <div class="bg-white border rounded-3xl p-6 hover:shadow-lg transition cursor-pointer">

                    <p class="text-gray-400">
                        Community Fund
                    </p>

                    <h3 class="text-4xl font-black mt-2">
                        RM {{ number_format($communityFund, 2) }}
                    </h3>

                </div>

            </a>

        </div>
        <!-- ACTIVE CAMPAIGNS -->

        <div class="mb-8">

            <div class="flex justify-between items-center mb-6">

                <h2 class="text-2xl font-bold">
                    Active Campaigns
                </h2>

            </div>

            @if(count($campaigns) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($campaigns as $campaign)

                        @if($campaign['status'] == 'Approved')

                            <div class="bg-white border rounded-2xl p-6 h-full">

                                <div class="flex flex-col h-full">

                                    <h3 class="text-xl font-bold">
                                        {{ $campaign['name'] }}
                                    </h3>

                                    <p class="text-gray-500 mt-2">
                                        {{ $campaign['location'] }}
                                    </p>
@if(!empty($campaign['description']))
    <p class="mt-2 text-gray-600 break-words">
        {{ \Illuminate\Support\Str::limit($campaign['description'], 100) }}
    </p>
@endif



                                    <div class="mt-2">

                                        <div class="flex items-center gap-4">

                                            <span class="font-semibold text-red-500">
                                                Target:
                                                RM {{ number_format((float) $campaign['target'], 2) }}
                                            </span>

                                            <span class="font-semibold text-green-600">
                                                {{ $campaign['progress'] }}%
                                            </span>

                                        </div>

                                        <div class="mt-2">

                                            <span class="font-medium">
                                                Collected:
                                            </span>

                                            <span class="text-green-600 font-semibold">
                                                RM {{ number_format($campaign['collected'], 2) }}
                                            </span>

                                        </div>

                                        <div class="mt-1">

                                            <span class="font-medium">
                                                Remaining:
                                            </span>

                                            <span>
                                                RM {{ number_format($campaign['remaining'], 2) }}
                                            </span>

                                        </div>

                                    </div>

                                    <div class="mt-3">

                                        <div class="w-full bg-gray-200 rounded-full h-3">

                                            <div class="bg-green-500 h-3 rounded-full"
                                                style="width: {{ min(100, $campaign['progress']) }}%;">
                                            </div>

                                        </div>

                                        <div class="text-sm text-gray-500 mt-1">

                                            {{ $campaign['progress'] }}%
                                            funded

                                        </div>

                                    </div>

                                    <div class="mt-auto pt-4">

                                        <a href="/donor/donation?campaign={{ urlencode($campaign['name']) }}"
                                            class="block w-full text-center bg-red-500 text-white py-3 rounded-xl hover:bg-red-600">

                                            Donate Now

                                        </a>

                                    </div>

                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>

            @else

                <div class="bg-white border rounded-3xl p-8">

                    <p class="text-gray-500">
                        No active campaigns available.
                    </p>

                </div>

            @endif

        </div>

        <!-- RECENT DONATIONS -->

        <div class="bg-white border rounded-3xl p-8">

            <div class="flex justify-between items-center mb-6">

                <h2 class="text-2xl font-bold">
                    Recent Donations
                </h2>

                <a href="/donor/history" class="text-red-500 font-semibold">
                    View Full History →
                </a>

            </div>

            <table class="w-full">

                <thead>

                    <tr class="border-b">

                        <th class="text-left py-4">
                            Date & Time
                        </th>

                        <th class="text-left py-4">
                            Campaign
                        </th>

                        <th class="text-left py-4">
                            Amount
                        </th>

                        <th class="text-left py-4">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @if(count($history) > 0)

                        @foreach($history as $transaction)

                            <tr class="border-b">

                                <td class="py-4">

                                    {{ $transaction->created_at->format('d M Y') }}

                                    <div class="text-xs text-gray-500">

                                        {{ $transaction->created_at->format('h:i A') }}

                                    </div>

                                </td>

                                <td class="py-4">

                                    {{ $transaction->campaign_type }}

                                </td>

                                <td class="py-4">

                                    RM {{ number_format($transaction->amount, 2) }}

                                </td>

                                <td class="py-4">

                                    @if($transaction->status == 'Allocated')

                                        <span class="text-green-600 font-semibold">

                                            Completed

                                        </span>

                                    @else

                                        <span class="text-red-600 font-semibold">

                                            Failed

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    @else

                        <tr>

                            <td colspan="4" class="py-6 text-center text-gray-500">

                                No donation history available.

                            </td>

                        </tr>

                    @endif

                </tbody>

            </table>

        </div>
    </div>

@endsection