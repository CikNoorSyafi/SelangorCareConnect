@extends('layouts.donor')

@section('content')
    <style>
        .payment-card {
            border-color: #e5e7eb;
            transition: all .2s ease;
        }

        .payment-icon {
            color: #9ca3af;
        }

        .payment-card.active {
            border-color: #ef4444;
            background: #fff;
        }

        .payment-card.active .payment-icon {
            color: #ef4444;
        }
    </style>
    <div class="max-w-7xl mx-auto">

        <div class="mb-8">

            <h1>
                Make Online Contribution
            </h1>

            <p class="text-gray-500 mt-2">
                Securely donate to official state campaigns and initiatives.
            </p>

        </div>

        <div class="bg-white rounded-3xl border p-8 mb-8">

            <div class="flex items-center gap-3 mb-4">

                <span class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center font-bold">

                    1

                </span>

                <h2 class="font-bold text-2xl">

                    Select Campaign

                </h2>

            </div>

            <select name="campaign_name" id="campaign" class="w-full border rounded-xl px-4 py-3">

                <option value="">
                    Choose a campaign to support
                </option>

                @foreach($campaigns as $campaign)

                    @if($campaign['status'] == 'Approved')

                        <option value="{{ $campaign['name'] }}" {{ request('campaign') == $campaign['name'] ? 'selected' : '' }}>

                            {{ $campaign['name'] }}

                        </option>

                    @endif

                @endforeach

            </select>

        </div>

        <div class="bg-white border rounded-3xl p-8 mb-6">

            <h3 class="text-3xl font-black mb-2">
                Make Online Contribution
            </h3>

            <p class="text-gray-500 mb-8">
                Securely donate to the Community Fund.
            </p>

            <!-- STEP 1 -->

            <div class="mb-8">

                <div class="flex items-center mb-4">

                    <span class="bg-red-500 text-white w-6 h-6 rounded-full text-xs flex items-center justify-center mr-3">
                        1
                    </span>

                    <h4 class="font-bold">
                        Donation Amount (RM)
                    </h4>

                </div>

                <div class="grid grid-cols-4 gap-3">

                    <button type="button" class="amount-btn border-2 rounded-xl py-4 font-semibold" data-value="10">

                        RM 10

                    </button>

                    <button type="button" class="amount-btn border-2 rounded-xl py-4 font-semibold" data-value="50">

                        RM 50

                    </button>

                    <button type="button" class="amount-btn border-2 rounded-xl py-4 font-semibold" data-value="100">

                        RM 100

                    </button>

                    <input type="text" id="customAmount" placeholder="Other Amount" class="border rounded-xl px-4 py-3">
                </div>

            </div>

            <!-- STEP 2 -->

            <div class="mb-8">

                <div class="flex items-center mb-4">

                    <span class="bg-red-500 text-white w-6 h-6 rounded-full text-xs flex items-center justify-center mr-3">
                        2
                    </span>

                    <h4 class="font-bold">
                        Payment Method
                    </h4>

                </div>
                <style>
                    .amount-btn {
                        border-color: #e5e7eb;
                        transition: all .2s ease;
                    }

                    .amount-btn.active {
                        border-color: #ef4444;
                        color: #ef4444;
                        background: white;
                    }
                </style>
                <div class="grid md:grid-cols-3 gap-4">

                    <label class="payment-card cursor-pointer border-2 rounded-xl p-6 text-center block">

                        <input type="radio" name="payment_method" value="card" checked class="hidden payment-radio">

                        <div class="payment-icon mb-3">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />

                            </svg>

                        </div>

                        <div class="font-semibold">
                            Credit/Debit Card
                        </div>

                    </label>

                    <label class="payment-card cursor-pointer border-2 rounded-xl p-6 text-center block">

                        <input type="radio" name="payment_method" value="fpx" class="hidden payment-radio">

                        <div class="payment-icon mb-3">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 21h18M5 21V9l7-5 7 5v12M9 14h.01M12 14h.01M15 14h.01" />

                            </svg>

                        </div>

                        <div class="font-semibold">
                            FPX Online Banking
                        </div>

                    </label>

                    <label class="payment-card cursor-pointer border-2 rounded-xl p-6 text-center block">

                        <input type="radio" name="payment_method" value="wallet" class="hidden payment-radio">

                        <div class="payment-icon mb-3">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m0-6h4v6h-4a3 3 0 110-6z" />

                            </svg>

                        </div>

                        <div class="font-semibold">
                            E-Wallet
                        </div>

                    </label>

                </div>

            </div>

        </div>

        <div class="grid md:grid-cols-2 gap-6">

            <div class="bg-white border rounded-3xl p-6">

                <h4 class="font-bold text-red-500 mb-3">
                    🔒 Secure Payment
                </h4>

                <p class="text-gray-500 text-sm">

                    Your contribution is processed securely.
                    All transactions are encrypted and monitored for fraud prevention.

                </p>

            </div>

            <div class="bg-white border rounded-3xl p-6">

                <div class="flex justify-between mb-4">

                    <span>Total Donation</span>

                    <span id="totalDonation" class="font-bold text-2xl">

                        RM 0.00

                    </span>

                </div>

                <div class="flex justify-between mb-6">

                    <span>Processing Fee</span>

                    <span>RM 0.00</span>

                </div>

                <form method="POST" action="/donor/payment-gateway">

                    @csrf
                    <input type="hidden" id="campaignName" name="campaign_name">

                    <input type="hidden" id="donationAmount" name="amount">

                    <input type="hidden" id="paymentMethod" name="payment_method">

                    <button type="submit" class="w-full bg-red-500 text-white py-3 rounded-xl font-bold">

                        Proceed to Payment

                    </button>

                </form>



            </div>

        </div>

    </div>

    <script>

        let selectedAmount = 0;

        const totalDisplay =
            document.getElementById('totalDonation');

        document
            .querySelectorAll('.amount-btn')
            .forEach(button => {

                button.addEventListener('click', () => {

                    document
                        .querySelectorAll('.amount-btn')
                        .forEach(btn =>
                            btn.classList.remove('active')
                        );

                    button.classList.add('active');

                    selectedAmount =
                        parseFloat(
                            button.dataset.value
                        );

                    document
                        .getElementById('customAmount')
                        .value = '';

                    updateTotal();

                });

            });

        const customAmount =
            document.getElementById('customAmount');

        customAmount.addEventListener('input', function (e) {

            document
                .querySelectorAll('.amount-btn')
                .forEach(btn =>
                    btn.classList.remove('active')
                );

            let value =
                e.target.value.replace(/[^0-9.]/g, '');

            let numericValue =
                parseFloat(
                    value.replace(/,/g, '')
                );

            selectedAmount =
                numericValue || 0;

        });

        customAmount.addEventListener('blur', function (e) {

            let numericValue =
                parseFloat(
                    e.target.value.replace(/,/g, '')
                );

            if (!isNaN(numericValue)) {
                e.target.value =
                    numericValue.toLocaleString(
                        'en-MY',
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    );
            }

            updateTotal();

        });

        function updateTotal() {
            totalDisplay.innerHTML =
                'RM ' +
                selectedAmount.toLocaleString(
                    'en-MY',
                    {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );
        }

        document
            .querySelector('form')
            .addEventListener('submit', function () {

                document
                    .getElementById('donationAmount')
                    .value = selectedAmount;

                document
                    .getElementById('campaignName')
                    .value =
                    document
                        .getElementById('campaign')
                        .value;

                const selectedMethod =
                    document.querySelector(
                        'input[name="payment_method"]:checked'
                    );

                document
                    .getElementById('paymentMethod')
                    .value =
                    selectedMethod
                        ? selectedMethod.value
                        : '';

            });


        const paymentCards =
            document.querySelectorAll('.payment-card');

        paymentCards.forEach(card => {

            const radio =
                card.querySelector('.payment-radio');

            card.addEventListener('click', () => {

                paymentCards.forEach(c =>
                    c.classList.remove('active')
                );

                card.classList.add('active');

                radio.checked = true;

            });

        });

        document.addEventListener('DOMContentLoaded', () => {

            const checked =
                document.querySelector(
                    '.payment-radio:checked'
                );

            if (checked) {
                checked
                    .closest('.payment-card')
                    .classList.add('active');
            }

        });

    </script>
@endsection