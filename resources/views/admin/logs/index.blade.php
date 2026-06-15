@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Activity Logs</h1>
        <p class="text-sm text-gray-500">System-wide audit trail of user and system actions.</p>
    </div>

    {{-- Search --}}
    <form method="GET" action="/admin/logs" class="flex gap-3 mb-5">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search actor, action or description"
            class="border rounded px-3 py-2 text-sm w-80">
        <button class="bg-primary text-white px-4 py-2 rounded text-sm">Search</button>
    </form>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Date / Time</th>
                    <th class="px-4 py-3">Actor</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t">
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-800">{{ $log->actor_name ?? 'System' }}</span>
                            <span class="text-xs text-gray-400 block capitalize">{{ $log->actor_role }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-600">{{ $log->action }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">No activity logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

@endsection
