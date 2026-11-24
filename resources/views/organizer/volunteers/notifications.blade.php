@extends('layouts.volunteer')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h1 class="text-4xl font-black text-gray-900">Notifications</h1>
        <p class="text-gray-500 mt-2">View announcements and updates from organizers.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($notifications->isEmpty())
        <div class="bg-white border rounded-2xl p-8 text-center text-gray-500">
            No notifications available.
        </div>
    @else
        @foreach($notifications as $item)
            @if($item->notification)
                <div class="bg-white border rounded-2xl p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $item->notification->title }}</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $item->notification->created_at?->format('d M Y, H:i') ?? '—' }}
                            </p>
                        </div>
                        @if($item->is_read)
                            <span class="w-fit rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold">Read</span>
                        @else
                            <span class="w-fit rounded-full bg-amber-100 text-amber-700 px-3 py-1 text-xs font-semibold">Unread</span>
                        @endif
                    </div>

                    <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $item->notification->message }}</p>

                    <div class="flex flex-wrap gap-x-5 gap-y-1 text-xs text-gray-500">
                        <span>Type: {{ $item->notification->type }}</span>
                        <span>Campaign: {{ $item->notification->campaign?->name ?? 'General Announcement' }}</span>
                    </div>

                    @if(!$item->is_read)
                        <form method="POST" action="/volunteer/notifications/{{ $item->id }}/read">
                            @csrf
                            <button type="submit" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">
                                Mark as Read
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-gray-400">Read on {{ $item->updated_at?->format('d M Y, H:i') ?? '—' }}</p>
                    @endif
                </div>
            @endif
        @endforeach
    @endif
</div>
@endsection
