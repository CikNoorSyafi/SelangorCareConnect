@extends('layouts.organizer')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-2xl font-bold text-slate-900">Attendance Management</h1><p class="mt-1 text-sm text-slate-500">{{ $campaign->name }} · Record attendance for assigned volunteers.</p></div>
        <a href="/campaign" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Back to Campaigns</a>
    </div>

    @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><p class="mb-2 font-semibold">Please correct the attendance details:</p><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Present</p><p class="mt-1 text-2xl font-bold text-emerald-700">{{ $attendanceSummary['present'] }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Absent</p><p class="mt-1 text-2xl font-bold text-red-600">{{ $attendanceSummary['absent'] }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Not yet recorded</p><p class="mt-1 text-2xl font-bold text-slate-800">{{ $attendanceSummary['not_recorded'] }}</p></div>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-end sm:justify-between">
            <div><h2 class="font-semibold text-slate-800">Volunteer Attendance Register</h2><p class="mt-1 text-xs text-slate-500">Select Present or Absent for each assigned volunteer. Leave as Not recorded when no attendance has been taken. Attendance is saved per campaign and date.</p></div>
            <form method="GET" action="/campaign/attendance/{{ $campaign->id }}" class="flex items-end gap-2"><label class="text-xs font-semibold text-slate-600">Attendance date<input type="date" name="date" value="{{ $attendanceDate }}" max="{{ today()->toDateString() }}" class="mt-1 block rounded-lg border border-slate-300 px-3 py-2 text-sm"></label><button class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Load Date</button></form>
        </div>

        @if($volunteers->isEmpty())
            <div class="p-10 text-center"><span class="material-symbols-outlined text-4xl text-slate-300">groups</span><p class="mt-2 font-semibold text-slate-700">No volunteers assigned</p><p class="mt-1 text-sm text-slate-500">Assign volunteers to this campaign before recording attendance.</p></div>
        @else
            <form method="POST" action="/campaign/attendance/store/{{ $campaign->id }}">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-white text-left text-xs font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3">Volunteer</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Shift</th><th class="px-4 py-3">Attendance</th><th class="px-4 py-3">Check In</th><th class="px-4 py-3">Check Out</th><th class="px-4 py-3">Hours</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                        @foreach($volunteers as $assignment)
                            @php
                                $volunteerId = $assignment->volunteer_id;
                                $record = $attendanceRecords->get($volunteerId);
                                $status = old("attendance.$volunteerId.status", $record->status ?? '');
                            @endphp
                            <tr class="align-middle">
                                <td class="px-4 py-4"><p class="font-semibold text-slate-800">{{ $assignment->volunteer->name ?? 'Volunteer unavailable' }}</p><p class="text-xs text-slate-500">{{ $assignment->volunteer->email ?? '' }}</p></td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $assignment->role->name ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $assignment->shift->name ?? '-' }}</td>
                                <td class="px-4 py-4"><select data-attendance-status name="attendance[{{ $volunteerId }}][status]" class="rounded-lg border border-slate-300 px-2 py-2 text-sm"><option value="" @selected($status === '')>Not recorded</option><option value="Present" @selected($status === 'Present')>Present</option><option value="Absent" @selected($status === 'Absent')>Absent</option></select></td>
                                <td class="px-4 py-4"><input data-attendance-time type="time" name="attendance[{{ $volunteerId }}][check_in]" value="{{ old("attendance.$volunteerId.check_in", $record?->check_in ? substr($record->check_in, 0, 5) : '') }}" class="w-28 rounded-lg border border-slate-300 px-2 py-2 text-sm"></td>
                                <td class="px-4 py-4"><input data-attendance-time type="time" name="attendance[{{ $volunteerId }}][check_out]" value="{{ old("attendance.$volunteerId.check_out", $record?->check_out ? substr($record->check_out, 0, 5) : '') }}" class="w-28 rounded-lg border border-slate-300 px-2 py-2 text-sm"></td>
                                <td class="px-4 py-4"><input type="number" min="0" max="24" step="1" name="attendance[{{ $volunteerId }}][hours_served]" value="{{ old("attendance.$volunteerId.hours_served", $record->hours_served ?? '') }}" placeholder="Auto" class="w-20 rounded-lg border border-slate-300 px-2 py-2 text-sm"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between"><p class="text-xs text-slate-500">Choose Not recorded to leave the volunteer unmarked or clear a previously saved record. For Present, check-in and check-out are required; leave Hours blank to calculate it from the time range.</p><button class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Save Attendance</button></div>
            </form>
        @endif
    </section>
</div>
<script>
(function () {
    function syncAttendanceRow(select) {
        const row = select.closest('tr');
        const present = select.value === 'Present';
        row.querySelectorAll('[data-attendance-time]').forEach((input) => {
            input.required = present;
            input.disabled = !present;
            if (!present) input.value = '';
        });
    }
    document.querySelectorAll('[data-attendance-status]').forEach((select) => {
        syncAttendanceRow(select);
        select.addEventListener('change', () => syncAttendanceRow(select));
    });
})();
</script>
@endsection
