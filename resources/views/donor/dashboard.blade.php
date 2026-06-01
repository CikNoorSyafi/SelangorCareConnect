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
        @php

            $totalContributed = 0;

            foreach ($history ?? [] as $transaction) {

                if ($transaction['status'] == 'SUCCESS') {

                    $totalContributed +=
                        $transaction['amount'];

                }

            }

        @endphp
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

            <a href="/campaign">

                <div class="bg-white border rounded-3xl p-6 hover:shadow-lg transition cursor-pointer">

                    <p class="text-gray-400">
                        Campaigns Supported
                    </p>

                    <h3 class="text-4xl font-black mt-2">
                        {{ count($campaigns) }}
                    </h3>

                </div>

            </a>

            <a href="/donor/fund">

                <div class="bg-white border rounded-3xl p-6 hover:shadow-lg transition cursor-pointer">

                    <p class="text-gray-400">
                        Community Fund
                    </p>

                    <h3 class="text-4xl font-black mt-2">
                        RM 25,000
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

                @foreach($campaigns as $campaign)

                    @if($campaign['status'] == 'Approved')

                        <div class="bg-white border rounded-3xl p-8 mb-4">

                            <div class="flex justify-between items-start">

                                <div>

                                    <h3 class="text-2xl font-bold">
                                        {{ $campaign['name'] }}
                                    </h3>

                                    <p class="text-gray-500 mt-2">
                                        {{ $campaign['location'] }}
                                    </p>

                                    <p class="mt-3 text-gray-600">
                                        {{ $campaign['description'] }}
                                    </p>

                                    <div class="mt-4">

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

                                        <div class="mt-1 text-gray-500">

                                            Volunteers:
                                            {{ count($campaign['volunteers'] ?? []) }}

                                        </div>

                                    </div>

                                    <div class="mt-3">

                                        <div class="w-full bg-gray-200 rounded-full h-3">

                                            <div class="bg-green-500 h-3 rounded-full" style="
                                                                                                                width:
                                                                                                                {{ min(100, $campaign['progress']) }}%;
                                                                                                            ">
                                            </div>

                                        </div>

                                        <div class="text-sm text-gray-500 mt-1">

                                            {{ $campaign['progress'] }}%
                                            funded

                                        </div>

                                    </div>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Volunteers:
                                        {{ count($campaign['volunteers'] ?? []) }}
                                    </p>

                                </div>

                                <div>

                                    <a href="/donor/donation?campaign={{ urlencode($campaign['name']) }}"
                                        class="bg-red-500 text-white px-6 py-3 rounded-xl inline-block">

                                        Donate Now

                                    </a>

                                </div>

                            </div>

                        </div>
                    @endif
                @endforeach

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

                        @foreach(array_reverse($history) as $transaction)

                            <tr class="border-b">

                                <td class="py-4">

                                    {{ $transaction['datetime']->format('d M Y') }}

                                    <div class="text-xs text-gray-500">

                                        {{ $transaction['datetime']->format('h:i A') }}

                                    </div>

                                </td>

                                <td class="py-4">

                                    {{ $transaction['campaign'] ?? 'Community Fund' }}

                                </td>

                                <td class="py-4">

                                    RM {{ number_format($transaction['amount'], 2) }}

                                </td>

                                <td class="py-4">

                                    @if($transaction['status'] == 'SUCCESS')

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