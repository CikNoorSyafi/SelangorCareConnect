@extends('layouts.organizer')

@section('content')

    <div class="space-y-8">
        @if(session('success'))

            <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-2xl">

                {{ session('success') }}

            </div>

        @endif

        <!-- PAGE HEADER -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

            <div>
                <h1 class="text-4xl font-black text-gray-900">
                    Donation Management
                </h1>

                <p class="text-gray-500 mt-2 text-lg">
                    Monitor collections and fund allocation across campaigns.
                </p>
            </div>

            <div class="flex gap-3">

                <!-- EXPORT -->
                <button
                    class="flex items-center gap-2 px-5 py-3 border border-gray-300 rounded-xl bg-white hover:bg-gray-50 transition">

                    <!-- DOWNLOAD SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v10m0 0l-4-4m4 4l4-4M4 20h16" />
                    </svg>

                    <span class="font-semibold">
                        Export Report
                    </span>
                </button>

                <!-- ADD -->
                <a href="/donation/create"
                    class="flex items-center gap-2 px-6 py-3 rounded-xl bg-red-600 text-white shadow-lg hover:bg-red-700 transition">

                    <!-- PLUS SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>

                    <span class="font-semibold">
                        Record Manual Donation
                    </span>
                </a>

            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            <!-- TOTAL COLLECTION -->
            <a href="/donation" class="bg-white border rounded-2xl p-6 hover:shadow-md transition">

                <div class="flex justify-between items-start">

                    <div class="p-3 rounded-xl bg-red-50">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a5 5 0 00-10 0v2m-2 0h14l1 10H4L5 9z" />
                        </svg>

                    </div>

                    <span class="text-green-600 font-bold text-sm">
                        +12.5%
                    </span>

                </div>

                <p class="text-gray-500 mt-6 text-sm font-semibold uppercase">
                    Total Collections (RM)
                </p>

                <h2 class="text-4xl font-black mt-2">
                    {{ $totalCollections }}
                </h2>

            </a>

            <!-- TOTAL ALLOCATED -->
            <div class="bg-white border rounded-2xl p-6 hover:shadow-md transition">

                <div class="flex justify-between items-start">

                    <div class="p-3 rounded-xl bg-blue-50">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3
                                                                                            3-1.343 3-3-1.343-3-3-3z" />
                        </svg>

                    </div>

                    <span class="text-gray-400 font-bold text-sm">
                        85% of total
                    </span>

                </div>

                <p class="text-gray-500 mt-6 text-sm font-semibold uppercase">
                    Total Allocated
                </p>

                <h2 class="text-4xl font-black mt-2">
                    {{ $totalAllocated }}
                </h2>

            </div>

            <!-- PENDING -->
            <a href="/donation?status=Pending" class="bg-white border rounded-2xl p-6 hover:shadow-md transition">

                <div class="flex justify-between items-start">

                    <div class="p-3 rounded-xl bg-yellow-50">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-yellow-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                        </svg>

                    </div>

                    <span class="text-yellow-600 font-bold text-sm">
                        Action Required
                    </span>

                </div>

                <p class="text-gray-500 mt-6 text-sm font-semibold uppercase">
                    Pending Verifications
                </p>

                <h2 class="text-4xl font-black mt-2">
                    {{ $pendingVerifications }}
                </h2>

            </a>

            <!-- CONTRIBUTORS -->
            <div class="bg-white border rounded-2xl p-6 hover:shadow-md transition">

                <div class="flex justify-between items-start">

                    <div class="p-3 rounded-xl bg-purple-50">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5" />
                        </svg>

                    </div>

                    <span class="text-purple-600 font-bold text-sm">
                        +156 this month
                    </span>

                </div>

                <p class="text-gray-500 mt-6 text-sm font-semibold uppercase">
                    Number of Contributors
                </p>

                <h2 class="text-4xl font-black mt-2">
                    {{ number_format($contributors) }}
                </h2>

            </div>

        </div>

        <!-- TABLE SECTION -->
        <div class="bg-white border rounded-2xl overflow-hidden">

            <!-- FILTER -->
            <div class="p-6 border-b bg-gray-50 flex flex-col lg:flex-row gap-4 justify-between">

                <form method="GET" action="/donation" class="flex flex-col md:flex-row gap-3 w-full">

                    <!-- SEARCH -->
                    <div class="relative w-full md:w-96">

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Filter by name, ID, or campaign..."
                            class="w-full border rounded-xl pl-4 pr-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                    </div>

                    <!-- STATUS -->
                    <select name="status" class="border rounded-xl px-4 py-3">

                        <option value="">
                            Status: All
                        </option>

                        <option value="Allocated" {{ request('status') == 'Allocated' ? 'selected' : '' }}>
                            Allocated
                        </option>

                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                            Pending
                        </option>

                    </select>

                    <button class="px-5 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">

                        Filter

                    </button>

                    <a href="/donation" class="px-5 py-3 rounded-xl border bg-white hover:bg-gray-50 text-center">

                        Clear

                    </a>

                </form>

                <div class="text-sm text-gray-500 whitespace-nowrap">
                    Showing {{ count($donations) }} records
                </div>

            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-red-50">

                        <tr class="text-left text-gray-600 uppercase text-sm">

                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Contributor</th>
                            <th class="px-6 py-4">Campaign</th>
                            <th class="px-6 py-5 text-left text-sm font-bold text-gray-600 uppercase">
                                TYPE
                            </th>
                            <th class="px-6 py-4">Amount</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($donations as $donation)

                            <tr class="border-t hover:bg-gray-50 transition">

                                <td class="px-6 py-5 text-gray-600">
                                    {{ $donation['date'] }}
                                </td>

                                <td class="px-6 py-5">

                                    <div class="font-bold">
                                        {{ $donation['contributor'] }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $donation['transaction_id'] }}
                                    </div>

                                </td>

                                <td class="px-6 py-5">
                                    {{ $donation['campaign'] }}
                                </td>
                                <td class="px-6 py-6">

                                    <span class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-semibold">

                                        {{ $donation['campaign_type'] ?? 'N/A' }}

                                    </span>

                                </td>

                                <td class="px-6 py-5 font-bold">
                                    RM {{ number_format((float) $donation['amount'], 2) }}
                                </td>

                                <td class="px-6 py-5">

                                    @if($donation['status'] == 'Allocated')

                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                                            Allocated
                                        </span>

                                    @else

                                        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-sm font-semibold">
                                            Pending
                                        </span>

                                    @endif

                                </td>

                                <td class="px-6 py-5">

                                    <div class="flex justify-center gap-3">

                                        <!-- VIEW -->
                                        <a href="/donation/view/{{ $donation['id'] }}"
                                            class="text-gray-500 hover:text-blue-600">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5
                                                                                                             12 5c4.478 0 8.268 2.943
                                                                                                             9.542 7-1.274 4.057-5.064 7-9.542
                                                                                                             7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>

                                        </a>

                                        <!-- EDIT -->
                                        <a href="/donation/edit/{{ $donation['id'] }}"
                                            class="text-gray-500 hover:text-yellow-600">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5h2m-1-1v2m-7
                                                                                                             9l9-9 4 4-9 9H5v-4z" />
                                            </svg>

                                        </a>

                                        <!-- DELETE -->
                                        <a href="/donation/delete/{{ $donation['id'] }}"
                                            onclick="return confirm('Delete this donation?')"
                                            class="text-gray-500 hover:text-red-600">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                                                                             a2 2 0 01-1.995-1.858L5 7m5
                                                                                                             4v6m4-6v6M1 7h22m-5-4H8
                                                                                                             m0 0L7 7m1-4h8l1 4" />
                                            </svg>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

        <!-- BOTTOM SECTION -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <!-- COLLECTION TREND -->
            <div class="xl:col-span-2 bg-white border rounded-2xl p-6">

                <div class="flex justify-between items-center mb-6">

                    <h3 class="text-2xl font-bold">
                        Collection Trends
                    </h3>



                </div>

                <!-- SIMPLE BAR CHART -->
                <div class="h-80 flex items-end gap-4">

                    @php
                        $maxAmount = collect($monthlyCollections)->max('amount');
                    @endphp

                    @foreach($monthlyCollections as $data)

                        @php
                            $height = ($data['amount'] / $maxAmount) * 100;
                        @endphp

                        <div class="flex flex-col items-center w-full">

                            <!-- AMOUNT -->
                            <span class="text-xs font-semibold text-gray-500 mb-2">

                                RM {{ number_format($data['amount']) }}

                            </span>

                            <!-- BAR -->
                            <div class="w-full bg-red-400 hover:bg-red-500 rounded-t-2xl transition-all duration-300"
                                style="height: {{ $height }}%; min-height: 40px;">

                            </div>

                            <!-- MONTH -->
                            <span class="text-sm font-semibold text-gray-500 mt-3 text-center">

                                {{ $data['month'] }}

                            </span>

                        </div>

                    @endforeach

                </div>

            </div>

            <!-- ALLOCATION -->
            <div class="bg-white border rounded-2xl p-6">

                <h3 class="text-2xl font-bold mb-8">
                    Allocation by Sector
                </h3>

                <div class="space-y-6">

                    <div>
                        <div class="flex justify-between text-sm font-semibold mb-2">
                            <span>Disaster Relief</span>
                            <span>45%</span>
                        </div>

                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="bg-red-600 h-full w-[45%]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-semibold mb-2">
                            <span>Education</span>
                            <span>30%</span>
                        </div>

                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="bg-yellow-500 h-full w-[30%]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-semibold mb-2">
                            <span>Medical Support</span>
                            <span>15%</span>
                        </div>

                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full w-[15%]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-semibold mb-2">
                            <span>Welfare Management</span>
                            <span>10%</span>
                        </div>

                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="bg-gray-400 h-full w-[10%]"></div>
                        </div>
                    </div>

                </div>

                <a href="/donation/allocation-details"
                    class="w-full block text-center border border-red-200 text-red-500 py-4 rounded-2xl font-semibold hover:bg-red-50 transition">

                    View Allocation Details

                </a>


            </div>

        </div>

    </div>


@endsection