@extends('layouts.organizer')

@section('content')

    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-10">

            <div>

                <h1 class="text-5xl font-black text-gray-900">

                    Allocation Details

                </h1>

                <p class="text-gray-500 text-xl mt-3">

                    View all campaign allocations grouped by sector.

                </p>

            </div>

            <!-- BACK BUTTON -->
            <a href="/donation" class="px-6 py-4 border rounded-2xl hover:bg-gray-50 transition text-lg">

                Back

            </a>

        </div>

        <!-- ALLOCATION GROUPS -->
        <div class="space-y-8">

            @foreach($allocationGroups as $type => $items)

                <div class="bg-white border rounded-3xl p-8">

                    <!-- TYPE TITLE -->
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">

                        {{ $type }}

                    </h2>

                    <!-- TABLE -->
                    <div class="overflow-x-auto">

                        <table class="w-full">

                            <thead>

                                <tr class="border-b text-gray-500 text-left">

                                    <th class="pb-4 text-lg font-semibold">
                                        Campaign
                                    </th>

                                    <th class="pb-4 text-lg font-semibold">
                                        Contributor
                                    </th>

                                    <th class="pb-4 text-lg font-semibold">
                                        Amount
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($items as $donation)

                                    <tr class="border-b last:border-0">

                                        <td class="py-6 text-lg font-semibold text-gray-800">

                                            {{ $donation['campaign'] }}

                                        </td>

                                        <td class="py-6 text-gray-600">

                                            {{ $donation['contributor'] }}

                                        </td>

                                        <td class="py-6 font-bold text-xl text-gray-900">

                                            RM {{ number_format((float) $donation['amount'], 2) }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

@endsection