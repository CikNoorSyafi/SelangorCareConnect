@extends('layouts.volunteer')

@section('content')
<div class="space-y-6">
    <div><h1 class="text-2xl font-bold text-slate-900">Attendance Tracking</h1><p class="mt-1 text-sm text-slate-500">Your attendance recorded by campaign organizers.</p></div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Total Volunteer Hours</p><p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($totalHours) }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Campaigns Attended</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($campaignsAttended) }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Recorded Attendance Rate</p><p class="mt-2 text-3xl font-bold text-emerald-600">{{ number_format($attendanceRate, 1) }}%</p></div>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5"><h2 class="font-semibold text-slate-800">Attendance History</h2><p class="mt-1 text-xs text-slate-500">Attendance information is updated when an organizer records a campaign session.</p></div>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Campaign</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Check In</th><th class="px-5 py-3">Check Out</th><th class="px-5 py-3">Hours Served</th></tr></thead><tbody class="divide-y divide-slate-100">
            @forelse($attendance as $record)
                <tr><td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $record->campaign->name ?? 'Campaign unavailable' }}</td><td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">{{ $record->attendance_date?->format('d M Y') }}</td><td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $record->status === 'Present' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">{{ $record->status }}</span></td><td class="px-5 py-4 text-sm text-slate-600">{{ $record->check_in ? substr($record->check_in, 0, 5) : '-' }}</td><td class="px-5 py-4 text-sm text-slate-600">{{ $record->check_out ? substr($record->check_out, 0, 5) : '-' }}</td><td class="px-5 py-4 text-sm text-slate-600">{{ $record->hours_served }} hrs</td></tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No attendance records have been recorded yet.</td></tr>
            @endforelse
        </tbody></table></div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $attendance->links() }}</div>
    </section>
</div>
@endsection
