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
                    Monitor donations and collections for your campaigns.
                </p>
            </div>

            <div class="flex gap-3">

                <!-- EXPORT -->
                <a href="/donation/export"
                    class="flex items-center gap-2 px-5 py-3 border border-gray-300 rounded-xl bg-white hover:bg-gray-50 transition">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v10m0 0l-4-4m4 4l4-4M4 20h16" />

                    </svg>

                    <span class="font-semibold">

                        Export Report

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


                </div>

                <p class="text-gray-500 mt-6 text-sm font-semibold uppercase">
                    Total Collections (RM)
                </p>

                <h2 class="text-4xl font-black mt-2">
                    RM {{ number_format((float) $totalCollections, 2) }}
                </h2>

            </a>

            <!-- TOTAL ALLOCATED -->
            <div class="bg-white border rounded-2xl p-6 hover:shadow-md transition">

                <div class="flex justify-between items-start">

                    <div class="p-3 rounded-xl bg-blue-50">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3
                                                                                                                                                            3-1.343 3-3-1.343-3-3-3z" />
                        </svg>

                    </div>


                </div>

                <p class="text-gray-500 mt-6 text-sm font-semibold uppercase">
                    Allocated Transactions
                </p>

                <h2 class="text-4xl font-black mt-2">
                    {{ number_format($totalAllocated) }}
                </h2>

            </div>


            <!-- PENDING PAYMENTS -->
            <div class="bg-white border rounded-2xl p-6 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div class="p-3 rounded-xl bg-amber-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-gray-500 mt-6 text-sm font-semibold uppercase">Pending Payments</p>
                <h2 class="text-4xl font-black mt-2">{{ number_format($pendingVerifications) }}</h2>
            </div>

            <!-- CONTRIBUTORS -->
            <div class="bg-white border rounded-2xl p-6 hover:shadow-md transition">

                <div class="flex justify-between items-start">

                    <div class="p-3 rounded-xl bg-purple-50">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5" />
                        </svg>

                    </div>


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
                            placeholder="Search contributor or campaign..."
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
                        <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>
                            Failed
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
                    Showing {{ $donations->total() }} records
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
                                    {{ $donation->created_at->format('d M Y H:i') }}
                                </td>

                                <td class="px-6 py-5">

                                    <div class="font-bold">
                                        {{ $donation['contributor'] }}
                                    </div>


                                </td>

                                <td class="px-6 py-5">
                                    {{ $donation->campaign->name }}
                                </td>

                                <td class="px-6 py-5">

                                    <span class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-semibold">

                                        {{ $donation->campaign_type }}

                                    </span>

                                </td>

                                <td class="px-6 py-5 font-bold">
                                    RM {{ number_format($donation->amount, 2) }}
                                </td>

                                <td class="px-6 py-5">

                                    @if($donation->status == 'Allocated')

                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                                            Allocated
                                        </span>

                                    @elseif($donation->status == 'Pending')

                                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-sm font-semibold">Pending</span>

                                    @elseif($donation->status == 'Failed')

                                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-semibold">Failed</span>

                                    @else
                                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm font-semibold">{{ $donation->status }}</span>
                                    @endif

                                </td>

                                <td class="px-6 py-5">

                                    <div class="flex justify-center gap-3">

                                        <!-- VIEW -->
                                        <a href="/donation/view/{{ $donation->id }}" class="text-gray-500 hover:text-blue-600">

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

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

        <div class="mt-4">{{ $donations->links() }}</div>

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
                        $maxAmount = max(1, (float) collect($monthlyCollections)->max('amount'));
                    @endphp

                    @foreach($monthlyCollections as $data)

                        @php
                            $height = $maxAmount > 0 ? (($data['amount'] / $maxAmount) * 100) : 0;
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

                <div class="space-y-5">
                    @forelse($allocationBySector as $sector)
                        <div>
                            <div class="mb-2 flex justify-between gap-3 text-sm font-semibold">
                                <span>{{ $sector['sector'] }}</span>
                                <span>{{ number_format($sector['percentage'], 1) }}% · RM {{ number_format($sector['amount'], 2) }}</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-red-500" style="width: {{ min(100, max(0, $sector['percentage'])) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No allocated donations are recorded for your campaigns yet.</p>
                    @endforelse
                </div>

                <a href="/donation/allocation-details"
                    class="w-full block text-center border border-red-200 text-red-500 py-4 rounded-2xl font-semibold hover:bg-red-50 transition">

                    View Allocation Details

                </a>


            </div>

        </div>

    </div>


@endsection