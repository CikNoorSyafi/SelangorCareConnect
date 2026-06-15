@extends('layouts.admin')

@section('content')

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Real-Time Reports</h1>
            <p class="text-sm text-gray-500">Live snapshot of system-wide metrics (UC10).</p>
        </div>
        <span class="text-xs text-gray-400">Generated {{ now()->timezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}</span>
    </div>

    {{-- Headline numbers --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-xs uppercase text-gray-400 font-semibold">Total Donations Allocated</p>
            <p class="text-2xl font-bold text-green-600 mt-1">RM {{ number_format($report['donation_total'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $report['donation_count'] }} transactions</p>
        </div>
        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-xs uppercase text-gray-400 font-semibold">Pending Verifications</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $report['pending_orgs'] }}</p>
            <p class="text-xs text-gray-400 mt-1">organizations awaiting review</p>
        </div>
        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-xs uppercase text-gray-400 font-semibold">Total Users</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ array_sum($report['users_by_role']) }}</p>
            <p class="text-xs text-gray-400 mt-1">across all roles</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Users by role --}}
        <div class="bg-white p-6 rounded-xl border shadow-sm">
            <h2 class="font-semibold text-gray-800 mb-4">Users by Role</h2>
            <div class="space-y-2 text-sm">
                @foreach ($report['users_by_role'] as $roleName => $count)
                    <div class="flex justify-between border-b pb-2 last:border-0">
                        <span class="text-gray-500">{{ $roleName }}</span>
                        <span class="font-semibold">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Campaigns by status --}}
        <div class="bg-white p-6 rounded-xl border shadow-sm">
            <h2 class="font-semibold text-gray-800 mb-4">Campaigns by Status</h2>
            <div class="space-y-2 text-sm">
                @forelse ($report['campaigns_by_status'] as $statusName => $count)
                    <div class="flex justify-between border-b pb-2 last:border-0">
                        <span class="text-gray-500">{{ $statusName }}</span>
                        <span class="font-semibold">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-gray-400">No campaigns yet.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Top campaigns --}}
    <div class="bg-white p-6 rounded-xl border shadow-sm mt-6">
        <h2 class="font-semibold text-gray-800 mb-4">Top Campaigns by Funds Raised</h2>
        <table class="w-full text-sm">
            <thead class="text-gray-500 text-left">
                <tr>
                    <th class="py-2">Campaign</th>
                    <th class="py-2">Type</th>
                    <th class="py-2 text-right">Raised (RM)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report['top_campaigns'] as $campaign)
                    <tr class="border-t">
                        <td class="py-2 font-medium text-gray-800">{{ $campaign->name }}</td>
                        <td class="py-2 text-gray-500">{{ $campaign->type }}</td>
                        <td class="py-2 text-right font-semibold">{{ number_format($campaign->raised ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-400">No campaign data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
