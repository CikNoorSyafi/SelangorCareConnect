@extends('layouts.organizer')

@section('content')
<div class="space-y-6">
    {{-- Page heading --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-red-600">Organizer Portal</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">
                Welcome back, {{ session('user.name', 'Organizer') }}. Here is the latest overview of the community programmes.
            </p>
        </div>
        <p class="text-sm text-gray-500">{{ now()->format('l, d F Y') }}</p>
    </div>

    {{-- Primary KPI cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ url('/campaign') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Total Campaigns</p>
                <span class="rounded-lg bg-red-50 p-2 text-red-600">▣</span>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($totalCampaigns) }}</p>
            <p class="mt-1 text-xs text-gray-500">Campaigns created by your account</p>
        </a>

        <a href="{{ url('/campaign?status=active') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Active Campaigns</p>
                <span class="rounded-lg bg-green-50 p-2 text-green-600">↗</span>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($activeCampaigns) }}</p>
            <p class="mt-1 text-xs text-gray-500">Recruiting and In Progress</p>
        </a>

        <a href="{{ url('/volunteers') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Volunteers Involved</p>
                <span class="rounded-lg bg-blue-50 p-2 text-blue-600">♙</span>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($totalVolunteers) }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ number_format($assignedVolunteers) }} assigned to your campaigns</p>
        </a>

        <a href="{{ url('/donation') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Allocated Donations</p>
                <span class="rounded-lg bg-amber-50 p-2 text-amber-600">RM</span>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">RM {{ number_format($totalAllocatedDonations, 2) }}</p>
            <p class="mt-1 text-xs text-gray-500">RM {{ number_format($thisMonthAllocatedDonations, 2) }} allocated this month</p>
        </a>
    </div>

    {{-- Secondary operational indicators --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <a href="{{ url('/campaign') }}" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm hover:border-red-200">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Fundraising Campaigns</span>
                <span class="text-lg font-bold text-gray-900">{{ number_format($fundraisingCampaigns) }}</span>
            </div>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-100">
                <div class="h-full rounded-full bg-amber-400" style="width: {{ $totalCampaigns > 0 ? ($fundraisingCampaigns / $totalCampaigns) * 100 : 0 }}%"></div>
            </div>
        </a>
        <a href="{{ url('/campaign') }}" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm hover:border-red-200">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Pending Volunteer Applications</span>
                <span class="text-lg font-bold text-gray-900">{{ number_format($pendingApplications) }}</span>
            </div>
            <p class="mt-2 text-xs text-gray-500">{{ number_format($totalApplications) }} total applications received</p>
        </a>
        <a href="{{ url('/donation') }}?status=Pending" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm hover:border-red-200">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Pending Donation Verification</span>
                <span class="text-lg font-bold text-gray-900">{{ number_format($pendingDonationCount) }}</span>
            </div>
            <p class="mt-2 text-xs text-gray-500">RM {{ number_format($pendingDonationAmount, 2) }} awaiting allocation</p>
        </a>
    </div>

    {{-- Charts and campaign lifecycle --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Donation Records Trend</h2>
                    <p class="mt-1 text-xs text-gray-500">Allocated donation records by creation month (last six months)</p>
                </div>
                <a href="{{ url('/donation') }}" class="text-sm font-semibold text-red-600 hover:underline">View donations</a>
            </div>
            <div class="relative h-72">
                <canvas id="donationTrendChart" aria-label="Allocated donation totals over the last six months"></canvas>
            </div>
        </section>

        <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-gray-900">Campaign Lifecycle</h2>
            <p class="mt-1 text-xs text-gray-500">Current campaign status breakdown</p>

            @php
                $campaignStatusRows = [
                    ['label' => 'Fundraising', 'count' => $fundraisingCampaigns, 'color' => 'bg-amber-400'],
                    ['label' => 'Recruiting', 'count' => $recruitingCampaigns, 'color' => 'bg-blue-500'],
                    ['label' => 'In Progress', 'count' => $inProgressCampaigns, 'color' => 'bg-green-500'],
                    ['label' => 'Completed', 'count' => $completedCampaigns, 'color' => 'bg-gray-400'],
                ];
            @endphp

            <div class="mt-6 space-y-5">
                @foreach($campaignStatusRows as $row)
                    @php $percentage = $totalCampaigns > 0 ? ($row['count'] / $totalCampaigns) * 100 : 0; @endphp
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ $row['label'] }}</span>
                            <span class="font-semibold text-gray-900">{{ number_format($row['count']) }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full {{ $row['color'] }}" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4 text-center">
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($approvedApplications) }}</p>
                    <p class="text-xs text-gray-500">Approved applicants</p>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($rejectedApplications) }}</p>
                    <p class="text-xs text-gray-500">Rejected applicants</p>
                </div>
            </div>
        </section>
    </div>

    {{-- Campaign funding table --}}
    <section class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <div>
                <h2 class="font-semibold text-gray-900">Campaign Funding Progress</h2>
                <p class="mt-1 text-xs text-gray-500">Top campaigns by allocated donations</p>
            </div>
            <a href="{{ url('/campaign') }}" class="text-sm font-semibold text-red-600 hover:underline">Manage campaigns</a>
        </div>

        @if($topCampaigns->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-gray-500">No campaigns have been created yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Campaign</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Collected</th>
                            <th class="px-5 py-3 font-semibold">Target</th>
                            <th class="px-5 py-3 font-semibold">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($topCampaigns as $campaign)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-900">{{ $campaign->name }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $campaign->location }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $campaign->status === 'Completed' ? 'bg-gray-100 text-gray-700' : ($campaign->status === 'In Progress' ? 'bg-green-50 text-green-700' : ($campaign->status === 'Recruiting' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700')) }}">
                                        {{ $campaign->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-medium text-gray-900">RM {{ number_format((float) ($campaign->allocated_donations_sum ?? 0), 2) }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $campaign->target ? 'RM ' . number_format((float) $campaign->target, 2) : 'Not set' }}</td>
                                <td class="px-5 py-4 min-w-40">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-full rounded-full bg-red-500" style="width: {{ $campaign->funding_progress }}%"></div>
                                        </div>
                                        <span class="w-10 text-right text-xs font-semibold text-gray-600">{{ $campaign->target ? $campaign->funding_progress . '%' : '—' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Recent records --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Recent Volunteer Applications</h2>
                    <p class="mt-1 text-xs text-gray-500">Latest applications submitted by volunteers</p>
                </div>
                <a href="{{ url('/campaign') }}" class="text-sm font-semibold text-red-600 hover:underline">View campaigns</a>
            </div>
            @forelse($recentApplications as $application)
                <div class="flex items-center justify-between gap-3 border-b border-gray-50 px-5 py-3 last:border-0">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $application->user->name ?? 'Volunteer account unavailable' }}</p>
                        <p class="mt-1 truncate text-xs text-gray-500">{{ $application->campaign->name ?? 'Campaign unavailable' }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                            {{ $application->status === 'Approved' ? 'bg-green-50 text-green-700' : ($application->status === 'Rejected' ? 'bg-red-50 text-red-700' : ($application->status === 'Withdrawn' ? 'bg-gray-100 text-gray-600' : 'bg-amber-50 text-amber-700')) }}">
                            {{ $application->status }}
                        </span>
                        <p class="mt-1 text-xs text-gray-400">{{ $application->created_at?->format('d M Y') }}</p>
                    </div>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-gray-500">No volunteer applications yet.</p>
            @endforelse
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Recent Donations</h2>
                    <p class="mt-1 text-xs text-gray-500">Latest contribution records</p>
                </div>
                <a href="{{ url('/donation') }}" class="text-sm font-semibold text-red-600 hover:underline">View donations</a>
            </div>
            @forelse($recentDonations as $donation)
                <div class="flex items-center justify-between gap-3 border-b border-gray-50 px-5 py-3 last:border-0">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $donation->contributor ?: ($donation->user->name ?? 'Anonymous contributor') }}</p>
                        <p class="mt-1 truncate text-xs text-gray-500">{{ $donation->campaign->name ?? ($donation->campaign_type ?: 'General Fund') }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-bold text-gray-900">RM {{ number_format((float) $donation->amount, 2) }}</p>
                        <p class="mt-1 text-xs {{ $donation->status === 'Allocated' ? 'text-green-600' : 'text-amber-600' }}">{{ $donation->status }}</p>
                    </div>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-gray-500">No donations have been recorded yet.</p>
            @endforelse
        </section>
    </div>

    {{-- Recent campaigns --}}
    <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-gray-900">Recently Created Campaigns</h2>
                <p class="mt-1 text-xs text-gray-500">Most recently added campaigns</p>
            </div>
            <a href="{{ url('/campaign') }}" class="text-sm font-semibold text-red-600 hover:underline">View all</a>
        </div>
        @if($recentCampaigns->isEmpty())
            <p class="py-6 text-center text-sm text-gray-500">No campaigns available.</p>
        @else
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                @foreach($recentCampaigns as $campaign)
                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="line-clamp-2 text-sm font-semibold text-gray-900">{{ $campaign->name }}</p>
                        <p class="mt-2 text-xs text-gray-500">{{ $campaign->type }} · {{ $campaign->location }}</p>
                        <p class="mt-2 text-xs font-semibold text-red-600">{{ $campaign->status }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <p class="text-xs text-gray-400">Dashboard figures are retrieved from the application database and refreshed whenever this page loads.</p>
</div>

{{-- Chart.js dashboard visualisation --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartElement = document.getElementById('donationTrendChart');
        if (!chartElement || typeof Chart === 'undefined') return;

        new Chart(chartElement, {
            type: 'line',
            data: {
                labels: @json($monthlyDonationLabels),
                datasets: [{
                    label: 'Allocated donations (RM)',
                    data: @json($monthlyDonationValues),
                    borderColor: '#ec1313',
                    backgroundColor: 'rgba(236, 19, 19, 0.10)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' RM ' + Number(context.parsed.y).toLocaleString('en-MY', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'RM ' + Number(value).toLocaleString('en-MY');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
