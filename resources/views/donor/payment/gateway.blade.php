@extends('layouts.donor')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-7">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-500">Secure checkout · Demonstration</p>
            <h1 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Payment Gateway</h1>
            <p class="mt-2 text-sm text-slate-500">Review your contribution and choose a simulated payment method.</p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-5 sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">Contribution summary</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900">{{ $donation->campaign?->name ?? 'General Fund' }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $donation->frequency === 'monthly' ? 'Monthly contribution preference' : 'One-Time contribution' }}</p>
                <p class="mt-4 text-3xl font-black text-red-600">RM {{ number_format((float) $donation->amount, 2) }}</p>
            </div>

            <div class="space-y-5 p-6 sm:p-8">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Donor</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $donation->contributor }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Receipt email</p>
                        <p class="mt-1 break-all font-semibold text-slate-800">{{ $donation->receipt_email ?: 'Not provided' }}</p>
                    </div>
                </div>

                <div>
                    <label for="gatewayPaymentMethod" class="mb-2 block text-sm font-semibold text-slate-700">Payment method</label>
                    <select id="gatewayPaymentMethod" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-red-400 focus:ring-4 focus:ring-red-100">
                        <option value="card">Credit / Debit Card</option>
                        <option value="fpx">FPX Online Banking</option>
                        <option value="wallet">E-Wallet</option>
                    </select>
                </div>

                @if($donation->dedication)
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Dedication / note</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $donation->dedication }}</p>
                    </div>
                @endif

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800">
                    This is a simulated payment gateway for project demonstration. Do not enter real card or banking credentials. Selecting Monthly records a monthly preference only; automatic recurring payments are not enabled.
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <form id="approvePaymentForm" method="POST" action="{{ route('donor.payment.success', $donation->id) }}">
                        @csrf
                        <input type="hidden" name="payment_method" value="card">
                        <button type="submit" class="w-full rounded-xl bg-emerald-600 px-5 py-3.5 font-bold text-white transition hover:bg-emerald-700">Approve Transaction</button>
                    </form>
                    <form id="rejectPaymentForm" method="POST" action="{{ route('donor.payment.failed', $donation->id) }}">
                        @csrf
                        <input type="hidden" name="payment_method" value="card">
                        <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50 px-5 py-3.5 font-bold text-red-700 transition hover:bg-red-100">Reject Transaction</button>
                    </form>
                </div>
                <a href="{{ $donation->campaign_id ? route('donor.donation', ['campaign_id' => $donation->campaign_id]) : route('donor.fund') }}" class="block text-center text-sm font-semibold text-slate-500 hover:text-red-600">Cancel and return to donation form</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector = document.getElementById('gatewayPaymentMethod');
            ['approvePaymentForm', 'rejectPaymentForm'].forEach((formId) => {
                const form = document.getElementById(formId);
                form?.addEventListener('submit', function () {
                    const hidden = form.querySelector('input[name="payment_method"]');
                    if (hidden && selector) hidden.value = selector.value;
                });
            });
        });
    </script>
@endsection
