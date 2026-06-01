@extends('layouts.donor')

@section('content')

    <div class="max-w-4xl mx-auto">

        <h1 class="text-4xl font-black mb-6">
            Payment Gateway Simulation
        </h1>

        <div class="bg-white rounded-3xl p-8">

            <p>
                Amount:
                RM {{ number_format(session('payment_amount', 0), 2) }}
            </p>

            <p>
                Method:
                {{ session('payment_method') }}
            </p>

            <p>
                Reference:
                {{ session('payment_reference') }}
            </p>

            <div class="mt-8 flex gap-4">

                <form method="POST" action="/donor/payment/success">

                    @csrf

                    <button class="bg-green-500 text-white px-6 py-3 rounded">

                        Approve Transaction

                    </button>

                </form>

                <form method="POST" action="/donor/payment/fail">

                    @csrf

                    <button class="bg-red-500 text-white px-6 py-3 rounded">

                        Reject Transaction

                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection