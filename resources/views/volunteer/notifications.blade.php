@extends('layouts.volunteer')

@section('content')
<div class="space-y-6">
    <div><h1 class="text-2xl font-bold text-slate-900">Notifications</h1><p class="mt-1 text-sm text-slate-500">Updates about your campaign applications and volunteer assignments.</p></div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        @forelse($notifications as $userNotification)
            @php($notification = $userNotification->notification)
            @if($notification)
                <article class="flex flex-col gap-3 border-b border-slate-100 p-5 last:border-0 sm:flex-row sm:items-start sm:justify-between {{ $userNotification->is_read ? 'bg-white' : 'bg-blue-50/50' }}">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2"><h2 class="font-semibold text-slate-900">{{ $notification->title }}</h2>@if(!$userNotification->is_read)<span class="rounded-full bg-blue-100 px-2 py-1 text-[11px] font-bold text-blue-700">NEW</span>@endif</div>
                        <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-slate-600">{{ $notification->message }}</p>
                        @if($notification->campaign)<p class="mt-2 text-xs text-slate-500">Campaign: {{ $notification->campaign->name }}</p>@endif
                        <p class="mt-2 text-xs text-slate-400">{{ optional($userNotification->created_at)->format('d M Y, h:i A') }}</p>
                    </div>
                    @if(!$userNotification->is_read)
                        <form method="POST" action="/volunteer/notifications/{{ $userNotification->id }}/read">@csrf<button class="shrink-0 rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50">Mark as Read</button></form>
                    @else
                        <span class="shrink-0 text-xs font-medium text-slate-400">Read</span>
                    @endif
                </article>
            @endif
        @empty
            <div class="p-12 text-center"><span class="material-symbols-outlined text-4xl text-slate-300">notifications_off</span><p class="mt-3 font-semibold text-slate-700">No notifications yet</p><p class="mt-1 text-sm text-slate-500">You will see updates here when an organizer reviews your application or assigns you to a campaign.</p></div>
        @endforelse
    </section>
</div>
@endsection
