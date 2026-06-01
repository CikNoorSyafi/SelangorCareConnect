@extends('layouts.donor')

@section('content')

    <div class="max-w-7xl mx-auto">

        <div class="mb-8">
            <h1 class="text-5xl font-black">
                Donation History
            </h1>

            <p class="text-gray-500 mt-2">
                View all your contributions.
            </p>
        </div>
        @php

            $totalDonated = 0;

            $latestAmount = 0;

            foreach ($history as $transaction) {

                if ($transaction['status'] == 'SUCCESS') {

                    $totalDonated += $transaction['amount'];

                }

            }

            if (count($history) > 0) {

                $latestAmount =
                    end($history)['amount'];

            }

        @endphp

        <div class="grid grid-cols-3 gap-6 mb-8">

            <div class="bg-white rounded-2xl border p-6">

                <p class="text-gray-500 text-sm uppercase">
                    Total Donated
                </p>

                <h2 class="text-4xl font-bold text-red-500 mt-2">

                    RM {{ number_format($totalDonated, 2) }}

                </h2>

            </div>

            <div class="bg-white rounded-2xl border p-6">

                <p class="text-gray-500 text-sm uppercase">
                    Total Transactions
                </p>

                <h2 class="text-4xl font-bold mt-2">

                    {{ count($history) }}

                </h2>

            </div>

            <div class="bg-white rounded-2xl border p-6">

                <p class="text-gray-500 text-sm uppercase">
                    Latest Contribution
                </p>

                <h2 class="text-4xl font-bold mt-2">

                    RM {{ number_format($latestAmount, 2) }}

                </h2>

            </div>

        </div>


        <div class="bg-white border rounded-3xl mb-6 overflow-hidden">

            <div class="p-6 flex flex-wrap gap-4">

                <input type="text" id="historySearch" placeholder="Filter by reference, campaign, or method..."
                    class="border rounded-xl px-4 py-3 w-96">

                <select onchange="window.location.href='/donor/history?status=' + this.value"
                    class="border rounded-xl px-4 py-3">

                    <option value="">
                        All Status
                    </option>

                    <option value="SUCCESS" {{ request('status') == 'SUCCESS' ? 'selected' : '' }}>
                        Completed
                    </option>

                    <option value="FAILED" {{ request('status') == 'FAILED' ? 'selected' : '' }}>
                        Failed
                    </option>

                </select>

            </div>

        </div>

        <div class="bg-white rounded-3xl border overflow-hidden shadow-sm">
            @php

                $page = request('page', 1);

                $perPage = 8;

                $historyReversed =
                    array_reverse($history, true);

                $totalRecords =
                    count($historyReversed);

                $totalPages =
                    ceil(
                        $totalRecords /
                        $perPage
                    );

                $offset =
                    ($page - 1) * $perPage;

                $pagedHistory =
                    array_slice(
                        $historyReversed,
                        $offset,
                        $perPage,
                        true
                    );

            @endphp

            <table class="w-full">

                <thead class="bg-red-50">

                    <tr>

                        <th class="text-left p-5">
                            Date
                        </th>

                        <th class="text-left p-5">
                            Reference No.
                        </th>

                        <th class="text-left p-5">
                            Fund
                        </th>

                        <th class="text-left p-5">
                            Amount
                        </th>

                        <th class="text-left p-5">
                            Payment Method
                        </th>

                        <th class="text-left p-5">
                            Status
                        </th>

                        <th class="text-left p-5">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody id="historyTable">

                    @if(count($history) > 0)

                        @foreach($pagedHistory as $index => $transaction)

                            <tr class="border-b hover:bg-gray-50 transition">

                                <td class="p-5">

                                    {{ $transaction['datetime']->format('d M Y') }}

                                </td>

                                <td class="p-5">

                                    {{ $transaction['reference'] }}

                                </td>

                                <td class="p-5">

                                    {{ $transaction['campaign'] ?? 'Community Fund' }}

                                </td>

                                <td class="p-5">

                                    RM {{ number_format($transaction['amount'], 2) }}

                                </td>

                                <td class="p-5">

                                    {{ $transaction['method'] }}

                                </td>

                                <td class="p-5">

                                    @if($transaction['status'] == 'SUCCESS')

                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                            Completed
                                        </span>

                                    @else

                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
                                            Failed
                                        </span>

                                    @endif

                                </td>

                                <td class="p-5">

                                    <div class="flex gap-2">

                                        <a href="/donor/history/view/{{ $index }}" title="View Details"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0
                                                                                                                                                     3 3 0 016 0zm7.938 0
                                                                                                                                                     C20.73 16.338 16.803 19
                                                                                                                                                     12 19c-4.803 0-8.73-2.662
                                                                                                                                                     -10.938-7
                                                                                                                                                     C3.27 7.662 7.197 5
                                                                                                                                                     12 5c4.803 0 8.73 2.662
                                                                                                                                                     10.938 7z" />

                                            </svg>

                                        </a>

                                        @if($transaction['status'] == 'SUCCESS')

                                            <a href="/donor/download-receipt/{{ $index }}" title="Download Receipt"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-600 hover:bg-red-200">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">

                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v10m0 0l-4-4m4 4l4-4m-9 8h10" />

                                                </svg>

                                            </a>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    @else

                        <tr>

                            <td colspan="7" class="py-6 text-center text-gray-500">

                                No donation history available.

                            </td>

                        </tr>

                    @endif

                </tbody>

            </table>

            <div class="px-6 py-4 border-t text-gray-500" id="recordCount">

                Showing

                {{ $offset + 1 }}

                to

                {{ min($offset + $perPage, $totalRecords) }}

                of

                {{ $totalRecords }}

                donation records

            </div>

            <div class="flex justify-end gap-3 p-6">

                @for($i = 1; $i <= $totalPages; $i++)

                        <a href="?status={{ request('status') }}&page={{ $i }}" class="w-12 h-12 flex items-center justify-center rounded-xl font-semibold transition

                        {{ $page == $i
                    ? 'bg-red-500 text-white shadow-md'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">

                            {{ $i }}

                        </a>

                @endfor

            </div>
        </div>

    </div>

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const search =
                    document.getElementById(
                        'historySearch'
                    );

                const rows =
                    document.querySelectorAll(
                        '#historyTable tr'
                    );

                function filterTable() {

                    const searchValue =
                        search.value.toLowerCase();

                    rows.forEach(row => {

                        const text =
                            row.innerText.toLowerCase();

                        if (
                            text.includes(searchValue)
                        ) {

                            row.style.display = '';

                        }
                        else {

                            row.style.display = 'none';

                        }

                    });

                }

                search.addEventListener(
                    'keyup',
                    filterTable
                );

            }
        );

    </script>
@endsection