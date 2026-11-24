@extends('layouts.donor')

@section('content')
    @php
        $isSuccess = $donation->status === 'Allocated';
        $isPending = $donation->status === 'Pending';
        $fundName = $donation->campaign?->name ?? ($donation->campaign_id ? ($donation->campaign_type ?: 'Campaign Fund') : 'General Fund');
    @endphp

    <div class="mx-auto max-w-5xl py-8">
        <div class="mb-8 text-center">
            @if($isSuccess)
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-4xl text-green-600">✓</div>
                <h1 class="text-4xl font-black">Payment Successful</h1>
                <p class="mt-2 text-gray-500">Thank you for supporting SelangorCareConnect+.</p>
            @elseif($isPending)
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-amber-100 text-4xl text-amber-600">…</div>
                <h1 class="text-4xl font-black">Payment Pending</h1>
                <p class="mt-2 text-gray-500">Your transaction has not been completed yet.</p>
            @else
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-red-100 text-4xl text-red-600">×</div>
                <h1 class="text-4xl font-black">Payment Unsuccessful</h1>
                <p class="mt-2 text-gray-500">The simulated payment was rejected. No successful donation was recorded.</p>
            @endif
        </div>

        @if(session('info'))
            <div class="mb-5 rounded-xl bg-blue-50 p-4 text-blue-700">{{ session('info') }}</div>
        @endif

        <div class="overflow-hidden rounded-2xl border bg-white shadow-sm">
            <div class="flex flex-col justify-between gap-4 bg-red-50 px-8 py-6 sm:flex-row sm:items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-red-500">Transaction Receipt</p>
                    <h2 class="mt-2 text-4xl font-black">RM {{ number_format((float) $donation->amount, 2) }}</h2>
                </div>
                <span class="w-fit rounded-lg px-4 py-2 text-sm font-semibold {{ $isSuccess ? 'bg-green-100 text-green-700' : ($isPending ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                    {{ $donation->status === 'Allocated' ? 'Paid Successfully' : $donation->status }}
                </span>
            </div>

            <div class="grid gap-6 p-8 sm:grid-cols-2">
                <div><p class="text-sm text-gray-500">Donor</p><p class="mt-1 font-semibold">{{ $donation->contributor }}</p></div>
                <div><p class="text-sm text-gray-500">Fund</p><p class="mt-1 font-semibold">{{ $fundName }}</p></div>
                <div><p class="text-sm text-gray-500">Date &amp; Time</p><p class="mt-1 font-semibold">{{ $donation->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y h:i A') }}</p></div>
                <div><p class="text-sm text-gray-500">Payment Method</p><p class="mt-1 font-semibold">{{ $donation->payment_method }}</p></div>
                <div><p class="text-sm text-gray-500">Contribution Frequency</p><p class="mt-1 font-semibold">{{ ($donation->frequency ?? 'one_time') === 'monthly' ? 'Monthly preference' : 'One-Time' }}</p></div>
                @if($donation->receipt_email)
                    <div><p class="text-sm text-gray-500">Receipt Email</p><p class="mt-1 break-all font-semibold">{{ $donation->receipt_email }}</p></div>
                @endif
            </div>
        </div>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            @if($isSuccess)
                <a href="{{ route('donor.receipt', $donation->id) }}" class="rounded-xl bg-red-500 px-6 py-3 font-semibold text-white hover:bg-red-600">Download Receipt</a>
            @elseif($isPending)
                <a href="{{ route('donor.payment.gateway', $donation->id) }}" class="rounded-xl bg-amber-500 px-6 py-3 font-semibold text-white">Continue Payment</a>
            @endif
            <a href="{{ route('donor.history') }}" class="rounded-xl border px-6 py-3 font-semibold hover:bg-gray-50">Donation History</a>
            <a href="{{ route('donor.dashboard') }}" class="rounded-xl border px-6 py-3 font-semibold hover:bg-gray-50">Return to Dashboard</a>
        </div>
    </div>
@endsection
