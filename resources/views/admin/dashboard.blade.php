@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Administrator Dashboard</h1>
        <p class="text-sm text-gray-500">System overview and recent activity.</p>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-xs uppercase text-gray-400 font-semibold">Total Users</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-xs uppercase text-gray-400 font-semibold">Pending Organizations</p>
            <p class="text-3xl font-bold text-red-500 mt-1">{{ $stats['pending_orgs'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-xs uppercase text-gray-400 font-semibold">Total Campaigns</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_campaigns'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-xl border shadow-sm">
            <p class="text-xs uppercase text-gray-400 font-semibold">Total Raised (RM)</p>
            <p class="text-3xl font-bold text-green-600 mt-1">
                {{ number_format($stats['total_raised'], 2) }}
            </p>
        </div>

    </div>

    {{-- User breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-xl border shadow-sm">
            <h2 class="font-semibold text-gray-800 mb-4">User Breakdown</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Organizers</span>
                    <span class="font-semibold">{{ $stats['organizers'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Volunteers</span>
                    <span class="font-semibold">{{ $stats['volunteers'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Donors</span>
                    <span class="font-semibold">{{ $stats['donors'] }}</span>
                </div>
            </div>
        </div>

        {{-- Recent activity feed --}}
        <div class="bg-white p-6 rounded-xl border shadow-sm lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-gray-800">Recent Activity</h2>
                <a href="/admin/logs" class="text-xs text-red-500 hover:underline">View all</a>
            </div>

            <div class="space-y-3">
                @forelse ($recentLogs as $log)
                    <div class="flex items-start justify-between text-sm border-b pb-2 last:border-0">
                        <div>
                            <p class="text-gray-800">{{ $log->description }}</p>
                            <p class="text-xs text-gray-400">{{ $log->actor_name ?? 'System' }} &middot; {{ $log->action }}</p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap ml-3">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No activity recorded yet.</p>
                @endforelse
            </div>
        </div>

    </div>

@endsection
