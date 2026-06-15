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

                if ($transaction->status == 'Allocated') {

                    $totalDonated += $transaction->amount;

                }

            }

            if (count($history) > 0) {

                $latestAmount =
                    $history->first()?->amount ?? 0;

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

            <form method="GET" action="/donor/history" class="p-6 flex flex-wrap gap-4">

                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search reference, campaign or payment method..." class="border rounded-xl px-4 py-3 w-96">

                <select name="status" class="border rounded-xl px-4 py-3">

                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>
                        All Status
                    </option>

                    <option value="Allocated" {{ request('status') == 'Allocated' ? 'selected' : '' }}>
                        Completed
                    </option>

                    <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>
                        Failed
                    </option>

                </select>

                <button type="submit" class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600">
                    Search
                </button>

                <a href="/donor/history" class="px-6 py-3 bg-gray-100 rounded-xl hover:bg-gray-200">
                    Reset
                </a>

            </form>

        </div>

        <div class="bg-white rounded-3xl border overflow-hidden shadow-sm">
            @php

                $totalRecords = $history->total();

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

                        @foreach($history as $transaction)

                            <tr class="border-b hover:bg-gray-50 transition">

                                <td class="p-5">

                                    {{ $transaction->created_at->format('d M Y') }}

                                </td>

                                <td class="p-5">

                                    {{ $transaction->transaction_id }}

                                </td>

                                <td class="p-5">

                                    {{ $transaction->campaign_type ?? 'Community Fund' }}

                                </td>

                                <td class="p-5">

                                    RM {{ number_format($transaction->amount, 2) }}

                                </td>

                                <td class="p-5">

                                    {{ $transaction->payment_method }}

                                </td>

                                <td class="p-5">

                                    @if($transaction->status == 'Allocated')

                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                            Completed
                                        </span>

                                    @elseif($transaction->status == 'Failed')

                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
                                            Failed
                                        </span>

                                    @else

                                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                                            {{ $transaction->status }}
                                        </span>

                                    @endif

                                </td>

                                <td class="p-5">

                                    <div class="flex gap-2">

                                        <a href="/donor/history/view/{{ $transaction->id }}" title="View Details"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200">

                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">

                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644
                                                                               C3.423 7.51 7.36 4.5 12 4.5
                                                                               c4.638 0 8.573 3.007 9.963 7.178
                                                                               .07.207.07.431 0 .644
                                                                               C20.577 16.49 16.64 19.5 12 19.5
                                                                               c-4.638 0-8.573-3.007-9.964-7.178z" />

                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0
                                                                               3 3 0 016 0z" />

                                            </svg>

                                        </a>

                                        @if($transaction->status == 'Allocated')

                                            <a href="/donor/download-receipt/{{ $transaction->id }}" title="Download Receipt"
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
                    <tr id="noResultsRow" style="display:none;">
                        <td colspan="7" class="py-8 text-center text-gray-500">
                            No records found.
                        </td>
                    </tr>
                </tbody>

            </table>


            <div class="flex justify-between items-center px-8 py-5 border-t">

                <p class="text-gray-500 text-sm">

                    Showing

                    {{ $history->firstItem() ?? 0 }}

                    -

                    {{ $history->lastItem() ?? 0 }}

                    of

                    {{ $history->total() }}

                    transactions

                </p>

                <div class="flex gap-2">

                    @for ($i = 1; $i <= $history->lastPage(); $i++)

                                <a href="{{ $history->url($i) }}" class="w-10 h-10 flex items-center justify-center rounded border
                                                                    {{ $history->currentPage() == $i
                        ? 'bg-red-500 text-white border-red-500'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100' }}">

                                    {{ $i }}

                                </a>

                    @endfor

                </div>

            </div>

        </div>

    </div>
@endsection