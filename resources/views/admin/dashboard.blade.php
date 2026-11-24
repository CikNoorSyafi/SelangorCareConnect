@extends('layouts.admin')

@section('content')
    <div class="space-y-7">
        {{-- Page heading --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-red-600">SYSTEM OVERVIEW</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Administrator Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500">A live overview of users, campaigns, donations and volunteer activity.</p>
            </div>
            <div class="text-sm text-gray-500">
                <span class="material-symbols-outlined mr-1 align-middle text-base">calendar_today</span>
                {{ now()->format('l, d F Y') }}
            </div>
        </div>

        {{-- Key performance indicators --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Key statistics">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total registered users</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                    </div>
                    <span class="rounded-xl bg-blue-50 p-3 text-blue-600 material-symbols-outlined">group</span>
                </div>
                <p class="mt-4 text-xs text-gray-500">
                    <span class="font-semibold text-blue-600">{{ number_format($stats['new_users_this_month']) }}</span> registered this month
                </p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total campaigns</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_campaigns']) }}</p>
                    </div>
                    <span class="rounded-xl bg-purple-50 p-3 text-purple-600 material-symbols-outlined">campaign</span>
                </div>
                <p class="mt-4 text-xs text-gray-500">
                    <span class="font-semibold text-green-600">{{ number_format($stats['active_campaigns']) }}</span> recruiting or in progress
                </p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total funds allocated</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">RM {{ number_format($stats['total_raised'], 2) }}</p>
                    </div>
                    <span class="rounded-xl bg-green-50 p-3 text-green-600 material-symbols-outlined">payments</span>
                </div>
                <p class="mt-4 text-xs text-gray-500">
                    {{ number_format($stats['allocated_donations']) }} successful donation records
                </p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending applications</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['pending_applications']) }}</p>
                    </div>
                    <span class="rounded-xl bg-amber-50 p-3 text-amber-600 material-symbols-outlined">pending_actions</span>
                </div>
                <p class="mt-4 text-xs text-gray-500">
                    {{ number_format($stats['pending_donations']) }} donations awaiting allocation
                </p>
            </div>
        </section>

        {{-- Funding and user analytics --}}
        <section id="funding" class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm xl:col-span-2">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-gray-900">Fundraising overview</h2>
                        <p class="mt-1 text-xs text-gray-500">Allocated donations over the last six calendar months</p>
                    </div>
                    <span class="rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700">Live database data</span>
                </div>
                <div class="h-72">
                    <canvas id="donationTrendChart" aria-label="Monthly allocated donation trend"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-gray-900">Registered users</h2>
                <p class="mt-1 text-xs text-gray-500">Breakdown by account role</p>
                <div class="mx-auto mt-4 h-52 max-w-xs">
                    <canvas id="userRoleChart" aria-label="Registered users by role"></canvas>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach ($roleBreakdown as $role)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ $role['label'] }}</span>
                            <span class="font-semibold text-gray-900">{{ number_format($role['total']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Campaign summary and performance --}}
        <section id="campaigns" class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-gray-900">Campaign status</h2>
                        <p class="mt-1 text-xs text-gray-500">Campaigns grouped by their current database status</p>
                    </div>
                    <span class="material-symbols-outlined text-gray-400">donut_small</span>
                </div>

                @forelse ($campaignBreakdown as $status)
                    @php
                        $percentage = $stats['total_campaigns'] > 0
                            ? ($status['total'] / $stats['total_campaigns']) * 100
                            : 0;
                        $barColor = match (strtolower($status['label'])) {
                            'fundraising' => 'bg-amber-500',
                            'recruiting' => 'bg-blue-500',
                            'in progress' => 'bg-green-500',
                            'completed' => 'bg-emerald-700',
                            'pending' => 'bg-orange-500',
                            'approved' => 'bg-green-500',
                            'closed' => 'bg-gray-400',
                            default => 'bg-red-500',
                        };
                    @endphp
                    <div class="mb-4 last:mb-0">
                        <div class="mb-1.5 flex justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ $status['label'] }}</span>
                            <span class="text-gray-500">{{ number_format($status['total']) }} ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-10 text-center text-sm text-gray-400">No campaigns have been created yet.</p>
                @endforelse

                <div class="mt-6 grid grid-cols-3 gap-2 border-t border-gray-100 pt-4 text-center">
                    <div>
                        <p class="text-xl font-bold text-amber-600">{{ $stats['fundraising_campaigns'] }}</p>
                        <p class="text-[11px] text-gray-500">Fundraising</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-blue-600">{{ $stats['active_campaigns'] }}</p>
                        <p class="text-[11px] text-gray-500">Active</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-green-600">{{ $stats['completed_campaigns'] }}</p>
                        <p class="text-[11px] text-gray-500">Completed</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-bold text-gray-900">Top funded campaigns</h2>
                    <p class="mt-1 text-xs text-gray-500">Ranked by allocated donations</p>
                </div>

                <div class="space-y-4">
                    @forelse ($topCampaigns as $campaign)
                        @php
                            $collected = (float) ($campaign->collected_amount ?? 0);
                            $target = (float) ($campaign->target ?? 0);
                            $progress = $target > 0 ? min(100, ($collected / $target) * 100) : 0;
                        @endphp
                        <div>
                            <div class="mb-1 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-gray-800">{{ $campaign->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $campaign->status }}</p>
                                </div>
                                <p class="whitespace-nowrap text-sm font-semibold text-gray-900">RM {{ number_format($collected, 2) }}</p>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-green-500" style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="mt-1 text-right text-[11px] text-gray-400">
                                @if ($target > 0)
                                    {{ number_format($progress, 1) }}% of RM {{ number_format($target, 2) }} target
                                @else
                                    No funding target set
                                @endif
                            </p>
                        </div>
                    @empty
                        <p class="py-10 text-center text-sm text-gray-400">No campaign funding records available.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Recent transactions and latest campaigns --}}
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <div>
                        <h2 class="font-bold text-gray-900">Recent donations</h2>
                        <p class="mt-1 text-xs text-gray-500">Latest donation transactions recorded in the database</p>
                    </div>
                    <span class="material-symbols-outlined text-gray-400">receipt_long</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Contributor</th>
                                <th class="px-5 py-3 font-semibold">Fund / Campaign</th>
                                <th class="px-5 py-3 text-right font-semibold">Amount</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentDonations as $donation)
                                <tr>
                                    <td class="whitespace-nowrap px-5 py-3">
                                        <p class="font-medium text-gray-800">{{ $donation->user?->name ?? $donation->contributor ?? 'Anonymous' }}</p>
                                        <p class="text-xs text-gray-400">{{ $donation->created_at?->format('d M Y') }}</p>
                                    </td>
                                    <td class="max-w-[180px] px-5 py-3">
                                        <p class="truncate text-gray-700">{{ $donation->campaign?->name ?? $donation->campaign_type ?? 'Community Fund' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right font-semibold text-gray-900">
                                        RM {{ number_format((float) $donation->amount, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3">
                                        @php
                                            $donationBadge = $donation->status === 'Allocated'
                                                ? 'bg-green-50 text-green-700'
                                                : 'bg-amber-50 text-amber-700';
                                        @endphp
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $donationBadge }}">{{ $donation->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-gray-400">No donation transactions recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-gray-900">Latest campaigns</h2>
                        <p class="mt-1 text-xs text-gray-500">Recently created campaigns</p>
                    </div>
                    <span class="material-symbols-outlined text-gray-400">campaign</span>
                </div>
                <div class="space-y-4">
                    @forelse ($recentCampaigns as $campaign)
                        <div class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $campaign->name }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $campaign->type }} · {{ $campaign->location }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ $campaign->volunteers_count }} assigned volunteers</span>
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-[11px] font-medium text-gray-600">{{ $campaign->status }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-400">No campaigns have been created yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Database-backed recent activity --}}
        <section id="activity" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">Recent system activity</h2>
                    <p class="mt-1 text-xs text-gray-500">Recent registrations, campaigns, donations and volunteer applications</p>
                </div>
                <span class="material-symbols-outlined text-gray-400">history</span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @forelse ($recentActivity as $activity)
                    <div class="flex gap-3 rounded-xl bg-gray-50 p-3">
                        <span class="material-symbols-outlined flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-red-600 shadow-sm">{{ $activity->icon }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-800">{{ $activity->title }}</p>
                            <p class="mt-0.5 text-sm text-gray-600">{{ $activity->description }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $activity->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="col-span-2 py-8 text-center text-sm text-gray-400">No activity is available yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Charts are rendered from values prepared by AdminController --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trendLabels = @json($donationTrend->pluck('label')->values());
            const trendValues = @json($donationTrend->pluck('total')->values());
            const roleLabels = @json($roleBreakdown->pluck('label')->values());
            const roleValues = @json($roleBreakdown->pluck('total')->values());

            const trendCanvas = document.getElementById('donationTrendChart');
            if (trendCanvas) {
                new Chart(trendCanvas, {
                    type: 'bar',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: 'Allocated donations (RM)',
                            data: trendValues,
                            backgroundColor: 'rgba(22, 163, 74, 0.18)',
                            borderColor: '#16a34a',
                            borderWidth: 2,
                            borderRadius: 7,
                            maxBarThickness: 42
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => ' RM ' + Number(context.raw).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => 'RM ' + Number(value).toLocaleString('en-MY')
                                },
                                grid: { color: '#f1f5f9' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            const roleCanvas = document.getElementById('userRoleChart');
            if (roleCanvas) {
                new Chart(roleCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: roleLabels,
                        datasets: [{
                            data: roleValues,
                            backgroundColor: ['#ef4444', '#3b82f6', '#eab308', '#8b5cf6'],
                            borderColor: '#ffffff',
                            borderWidth: 3,
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: true }
                        }
                    }
                });
            }
        });
    </script>
@endsection
