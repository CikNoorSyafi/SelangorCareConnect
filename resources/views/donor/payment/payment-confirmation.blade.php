@extends('layouts.donor')

@section('content')

    @php

        $status = session('payment_status', 'SUCCESS');

        $isSuccess = $status == 'SUCCESS';

    @endphp

    <div class="max-w-5xl mx-auto py-10">

        <!-- Status Icon -->

        <div class="text-center mb-8">

            @if($isSuccess)

                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-green-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />

                    </svg>

                </div>

                <h1 class="text-4xl font-black">
                    Payment Successful
                </h1>

                <p class="text-gray-500 mt-2">
                    Thank you for your generous contribution to SelangorCareConnect+.
                </p>

            @else

                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-red-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </div>

                <h1 class="text-4xl font-black">
                    Payment Unsuccessful
                </h1>

                <p class="text-gray-500 mt-2">
                    Unfortunately your contribution could not be processed.
                </p>

            @endif

        </div>

        <!-- Receipt Card -->

        <div class="bg-white rounded-2xl shadow border overflow-hidden">

            <!-- Header -->

            <div class="bg-red-50 px-8 py-6 flex justify-between items-center">

                <div>

                    <p class="text-xs font-bold uppercase tracking-widest text-red-500">
                        Transaction Receipt
                    </p>

                    <h2 class="text-4xl font-black mt-2">
                        RM {{ number_format(session('payment_amount', 0), 2) }}
                    </h2>

                </div>

                @if($isSuccess)

                    <span class="bg-green-50 text-green-600 px-4 py-2 rounded-lg font-medium">

                        ● Paid Successfully

                    </span>

                @else

                    <span class="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-medium">

                        ● Payment Failed

                    </span>

                @endif

            </div>

            <!-- Body -->

            <div class="p-8 grid md:grid-cols-2 gap-8">

                <div>

                    <p class="text-gray-500 text-sm">
                        Fund Name
                    </p>

                    <p class="font-semibold mt-1">
                        {{ session('campaign_name') }}
                    </p>

                </div>

                <div>

                    <p class="text-gray-500 text-sm">
                        Transaction ID
                    </p>

                    <p class="font-semibold mt-1">
                        {{ session('payment_reference') }}
                    </p>

                </div>

                <div>

                    <p class="text-gray-500 text-sm">
                        Date & Time
                    </p>

                    <p class="font-semibold mt-1">
                        {{ session('payment_datetime')->format('d M Y h:i A') }}
                    </p>

                </div>

                <div>

                    <p class="text-gray-500 text-sm">
                        Payment Method
                    </p>



                    <p class="font-semibold mt-1">
                        {{ session('payment_method') }}
                    </p>


                </div>

                @if(!$isSuccess)

                    <div class="col-span-2">

                        <p class="text-red-500 font-medium">

                            Reason:
                            Transaction rejected by issuing bank.

                        </p>

                    </div>

                @endif

            </div>

        </div>

        <!-- Buttons -->

        <div class="flex justify-center gap-4 mt-8">

            @if($isSuccess)

                <a href="/donor/download-receipt" class="bg-red-500 text-white px-6 py-3 rounded-xl">

                    Download Receipt

                </a>

            @endif

            <a href="/donor/dashboard">

                <button class="border px-6 py-3 rounded-xl">

                    Return to Dashboard

                </button>

            </a>

        </div>

    </div>

@endsection