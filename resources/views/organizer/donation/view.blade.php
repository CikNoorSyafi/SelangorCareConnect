@extends('layouts.organizer')

@section('content')

    <div class="max-w-4xl mx-auto bg-white border rounded-2xl p-8">

        <div class="flex justify-between items-start mb-8">

            <div>

                <h1 class="text-4xl font-black text-gray-900">
                    Donation Details
                </h1>

                <p class="text-gray-500 mt-2">
                    View complete donation information.
                </p>

            </div>

            <a href="/donation" class="px-5 py-3 rounded-xl border hover:bg-gray-50">

                Back

            </a>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-400">Contributor</p>
                <h3 class="text-xl font-bold mt-1">
                    {{ $donation['contributor'] }}
                </h3>
            </div>

            <div>
                <p class="text-sm text-gray-400">Transaction ID</p>
                <h3 class="text-xl font-bold mt-1">
                    {{ $donation['transaction_id'] }}
                </h3>
            </div>

            <!-- CAMPAIGN -->
            <div>

                <p class="text-sm text-gray-400">
                    Campaign
                </p>

                <h3 class="text-xl font-bold mt-1">

                    {{ $donation['campaign'] }}

                </h3>

            </div>

            <!-- CAMPAIGN TYPE -->
            <div>

                <p class="text-sm text-gray-400">
                    Campaign Type
                </p>

                <h3 class="text-xl font-bold mt-1">

                    {{ $donation['campaign_type'] ?? 'N/A' }}

                </h3>

            </div>

            <div>
                <p class="text-sm text-gray-400">Amount</p>
                <h3 class="text-xl font-bold mt-1">
                    RM {{ number_format((float) $donation['amount'], 2) }}
                </h3>
            </div>

            <div>
                <p class="text-sm text-gray-400">Status</p>
                <h3 class="text-xl font-bold mt-1">
                    {{ $donation['status'] }}
                </h3>
            </div>

            <div>
                <p class="text-sm text-gray-400">Date</p>
                <h3 class="text-xl font-bold mt-1">
                    {{ $donation['date'] }}
                </h3>
            </div>

        </div>

    </div>

@endsection