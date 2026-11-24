@extends('layouts.organizer')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="/communication" class="text-sm font-semibold text-red-600 hover:underline">&larr; Back to Communication Center</a>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mt-3">Notification Read Status</h1>
            <p class="text-gray-500 mt-2">See which recipients have read this notification and which are still unread.</p>
        </div>
        <span class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold {{ $notification->type === 'System' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
            {{ $notification->type }} Notification
        </span>
    </div>

    <div class="bg-white border rounded-2xl p-6 space-y-4">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $notification->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">Sent {{ $notification->created_at?->format('d M Y, H:i') ?? '—' }}</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700">{{ $notification->status }}</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl bg-gray-50 p-3">
                <span class="text-gray-500">Audience</span>
                <p class="font-semibold mt-1">{{ $notification->audience }}</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-3">
                <span class="text-gray-500">Campaign</span>
                <p class="font-semibold mt-1">{{ $notification->campaign?->name ?? 'General Announcement' }}</p>
            </div>
        </div>
        <div class="border-t pt-4">
            <p class="text-xs uppercase tracking-wide text-gray-500 font-bold mb-2">Message</p>
            <p class="text-gray-800 whitespace-pre-line leading-relaxed">{{ $notification->message }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border rounded-2xl p-5">
            <p class="text-sm text-gray-500">Total Recipients</p>
            <p class="text-3xl font-black mt-2">{{ $notification->recipient_count }}</p>
        </div>
        <div class="bg-white border border-green-200 rounded-2xl p-5">
            <p class="text-sm text-green-700">Read</p>
            <p class="text-3xl font-black text-green-700 mt-2">{{ $notification->read_count }}</p>
        </div>
        <div class="bg-white border border-amber-200 rounded-2xl p-5">
            <p class="text-sm text-amber-700">Not Yet Read</p>
            <p class="text-3xl font-black text-amber-700 mt-2">{{ $unreadCount }}</p>
        </div>
    </div>

    <div class="bg-white border rounded-2xl overflow-hidden">
        <div class="p-5 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold">Recipient Details</h2>
                <p class="text-sm text-gray-500 mt-1">Read status is updated when each recipient marks the notification as read in their account.</p>
            </div>
            <div class="flex gap-2 text-xs font-semibold">
                <span class="bg-green-100 text-green-700 rounded-full px-3 py-1">{{ $notification->read_count }} Read</span>
                <span class="bg-amber-100 text-amber-700 rounded-full px-3 py-1">{{ $unreadCount }} Unread</span>
            </div>
        </div>

        @if($recipients->isEmpty())
            <div class="p-10 text-center text-gray-500">No recipient records were created for this notification.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-4 text-left">#</th>
                            <th class="px-5 py-4 text-left">Recipient</th>
                            <th class="px-5 py-4 text-left">Email</th>
                            <th class="px-5 py-4 text-left">Read Status</th>
                            <th class="px-5 py-4 text-left">Read At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($recipients as $recipientRecord)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4 font-semibold text-gray-900">{{ $recipientRecord->user?->name ?? 'Deleted user' }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $recipientRecord->user?->email ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    @if($recipientRecord->is_read)
                                        <span class="inline-flex rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold">Read</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-100 text-amber-700 px-3 py-1 text-xs font-semibold">Not yet read</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-600">
                                    @if($recipientRecord->is_read)
                                        {{ $recipientRecord->updated_at?->format('d M Y, H:i') ?? 'Read' }}
                                    @else
                                        —
                                    @endif
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
