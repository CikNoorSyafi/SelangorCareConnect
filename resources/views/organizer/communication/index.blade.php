@extends('layouts.organizer')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-gray-900">Communication Center</h1>
            <p class="text-gray-500 mt-2 text-lg">Manage notifications and track which recipients have read your messages.</p>
        </div>
        <a href="/communication/create"
           class="inline-flex items-center justify-center bg-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition">
            + Create Notification
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white border rounded-2xl p-5">
            <p class="text-sm text-gray-500">Total Notifications</p>
            <p class="text-3xl font-black mt-2">{{ $totalNotifications }}</p>
        </div>
        <div class="bg-white border rounded-2xl p-5">
            <p class="text-sm text-gray-500">System Notifications</p>
            <p class="text-3xl font-black mt-2">{{ $systemNotifications }}</p>
        </div>
        <div class="bg-white border rounded-2xl p-5">
            <p class="text-sm text-gray-500">Manual Notifications</p>
            <p class="text-3xl font-black mt-2">{{ $manualNotifications }}</p>
        </div>
        <div class="bg-white border rounded-2xl p-5">
            <p class="text-sm text-gray-500">Sent</p>
            <p class="text-3xl font-black mt-2">{{ $sentNotifications }}</p>
        </div>
    </div>

    <div class="bg-white border rounded-2xl overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-5 border-b">
            <div>
                <h2 class="text-xl font-bold">Sent Notifications</h2>
                <p class="text-sm text-gray-500 mt-1">Select View to see the read status of every recipient.</p>
            </div>
            <form method="GET" action="/communication" class="flex gap-2">
                <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All types</option>
                    <option value="System" @selected(request('type') === 'System')>System</option>
                    <option value="Manual" @selected(request('type') === 'Manual')>Manual</option>
                </select>
                <button type="submit" class="bg-gray-900 text-white rounded-lg px-4 py-2 text-sm font-semibold">Filter</button>
                @if(request()->filled('type'))
                    <a href="/communication" class="border rounded-lg px-4 py-2 text-sm">Clear</a>
                @endif
            </form>
        </div>

        @if($notifications->isEmpty())
            <div class="p-10 text-center text-gray-500">
                No notifications found. Create a notification to start tracking recipient read status.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-red-50 text-gray-700">
                        <tr>
                            <th class="text-left px-5 py-4">Date</th>
                            <th class="text-left px-5 py-4">Notification</th>
                            <th class="text-left px-5 py-4">Type</th>
                            <th class="text-left px-5 py-4">Audience</th>
                            <th class="text-left px-5 py-4">Campaign</th>
                            <th class="text-center px-5 py-4">Recipients</th>
                            <th class="text-center px-5 py-4">Read</th>
                            <th class="text-left px-5 py-4">Status</th>
                            <th class="text-center px-5 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($notifications as $notification)
                            @php
                                $recipientCount = (int) $notification->recipient_count;
                                $readCount = (int) $notification->read_count;
                                $readPercent = $recipientCount > 0
                                    ? round(($readCount / $recipientCount) * 100)
                                    : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 whitespace-nowrap text-gray-600">
                                    {{ $notification->created_at?->format('d M Y, H:i') ?? '—' }}
                                </td>
                                <td class="px-5 py-4 min-w-56">
                                    <p class="font-semibold text-gray-900">{{ $notification->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $notification->message }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $notification->type === 'System' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $notification->type }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">{{ $notification->audience }}</td>
                                <td class="px-5 py-4">{{ $notification->campaign?->name ?? 'General Announcement' }}</td>
                                <td class="px-5 py-4 text-center">{{ $recipientCount }}</td>
                                <td class="px-5 py-4 min-w-32">
                                    <div class="text-center font-semibold">{{ $readCount }}/{{ $recipientCount }}</div>
                                    <div class="mt-2 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" style="width: {{ $readPercent }}%"></div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $notification->status === 'Delivered' ? 'bg-green-100 text-green-700' : ($notification->status === 'Failed' ? 'bg-red-100 text-red-700' : ($notification->status === 'Sent' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700')) }}">
                                        {{ $notification->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('communication.show', $notification->id) }}"
                                       class="inline-flex items-center justify-center rounded-lg bg-red-500 px-4 py-2 text-white font-semibold hover:bg-red-600">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
