@extends('layouts.donor')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-4xl font-black text-gray-900">Welcome Back, {{ session('user.name') }}</h1>
            <p class="mt-2 text-gray-500">Thank you for supporting community initiatives.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('donor.fund') }}" class="rounded-xl bg-red-500 px-5 py-3 font-semibold text-white hover:bg-red-600">General Fund</a>
            <a href="{{ route('donor.donation') }}" class="rounded-xl border border-red-500 px-5 py-3 font-semibold text-red-600 hover:bg-red-50">Support a Campaign</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="mb-8 grid gap-5 md:grid-cols-3">
        <a href="{{ route('donor.history') }}" class="rounded-2xl border bg-white p-6 transition hover:shadow-md">
            <p class="text-sm font-semibold uppercase text-gray-500">My Total Contributions</p>
            <p class="mt-2 text-3xl font-black text-red-500">RM {{ number_format($totalContributed, 2) }}</p>
            <p class="mt-2 text-xs text-gray-400">Successful donations made by you</p>
        </a>
        <a href="{{ route('donor.history') }}" class="rounded-2xl border bg-white p-6 transition hover:shadow-md">
            <p class="text-sm font-semibold uppercase text-gray-500">Campaigns Supported</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($campaignsSupported) }}</p>
            <p class="mt-2 text-xs text-gray-400">Distinct campaigns you supported successfully</p>
        </a>
        <a href="{{ route('donor.fund') }}" class="rounded-2xl border bg-white p-6 transition hover:shadow-md">
            <p class="text-sm font-semibold uppercase text-gray-500">General Fund Raised</p>
            <p class="mt-2 text-3xl font-black">RM {{ number_format($communityFund, 2) }}</p>
            <p class="mt-2 text-xs text-gray-400">Successful General Fund contributions platform-wide</p>
        </a>
    </div>

    <section class="mb-8">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Campaigns Accepting Donations</h2>
                <p class="mt-1 text-sm text-gray-500">Campaigns currently in the fundraising stage.</p>
            </div>
            <a href="{{ route('donor.donation') }}" class="font-semibold text-red-500 hover:underline">View all available campaigns →</a>
        </div>

        @include('organizer.partials.campaign-map-overview', [
            'mapId' => 'donor-campaign-map',
            'mapTitle' => 'Selangor Community Event Map',
            'mapDescription' => 'See community programmes across all nine Selangor administrative districts. Hover over a pin to view its event details.',
            'campaignMapPoints' => $campaignMapPoints,
        ])

        <div class="grid gap-5 lg:grid-cols-2">
            @forelse($campaigns as $campaign)
                <article class="flex h-full flex-col rounded-2xl border bg-white p-6">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-xl font-bold">{{ $campaign->name }}</h3>
                        <span class="shrink-0 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ $campaign->status === 'Approved' ? 'Open' : $campaign->status }}</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ $campaign->location }}</p>
                    @if($campaign->description)
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ \Illuminate\Support\Str::limit($campaign->description, 140) }}</p>
                    @endif

                    <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        <div><p class="text-gray-500">Funding Target</p><p class="mt-1 font-bold">RM {{ number_format((float) $campaign->target, 2) }}</p></div>
                        <div><p class="text-gray-500">Collected</p><p class="mt-1 font-bold text-green-600">RM {{ number_format($campaign->collected_amount, 2) }}</p></div>
                        <div><p class="text-gray-500">Remaining</p><p class="mt-1 font-semibold">RM {{ number_format($campaign->remaining_amount, 2) }}</p></div>
                        <div><p class="text-gray-500">Funded</p><p class="mt-1 font-semibold text-green-600">{{ number_format($campaign->funding_progress, 1) }}%</p></div>
                    </div>
                    <div class="mt-3 h-3 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-green-500" style="width: {{ $campaign->funding_progress }}%"></div>
                    </div>
                    <a href="{{ route('donor.donation', ['campaign_id' => $campaign->id]) }}" class="mt-5 block rounded-xl bg-red-500 px-5 py-3 text-center font-semibold text-white hover:bg-red-600">Donate to this Campaign</a>
                </article>
            @empty
                <div class="rounded-2xl border bg-white p-8 text-gray-500 lg:col-span-2">There are currently no campaigns accepting donations. Please check again later.</div>
            @endforelse
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border bg-white">
        <div class="flex items-center justify-between border-b p-6">
            <div>
                <h2 class="text-xl font-bold">My Recent Donations</h2>
                <p class="mt-1 text-sm text-gray-500">Your latest donation transactions.</p>
            </div>
            <a href="{{ route('donor.history') }}" class="font-semibold text-red-500 hover:underline">View History →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-sm">
                <thead class="bg-red-50 text-left text-gray-700">
                    <tr><th class="p-4">Date</th><th class="p-4">Fund / Campaign</th><th class="p-4">Amount</th><th class="p-4">Status</th></tr>
                </thead>
                <tbody>
                    @forelse($history as $transaction)
                        <tr class="border-t">
                            <td class="p-4">{{ $transaction->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y, h:i A') }}</td>
                            <td class="p-4">{{ $transaction->campaign?->name ?? ($transaction->campaign_id ? ($transaction->campaign_type ?: 'Campaign Fund') : 'General Fund') }}</td>
                            <td class="p-4 font-semibold">RM {{ number_format((float) $transaction->amount, 2) }}</td>
                            <td class="p-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $transaction->status === 'Allocated' ? 'bg-green-100 text-green-700' : ($transaction->status === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $transaction->status === 'Allocated' ? 'Completed' : $transaction->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center text-gray-500">You have not made any donations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
