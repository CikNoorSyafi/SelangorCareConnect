@extends('layouts.organizer')

@section('content')

    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-10">

            <div>

                <h1 class="text-5xl font-black text-gray-900">
                    Communication Center
                </h1>

                <p class="text-gray-500 text-xl mt-3">
                    Manage notifications and announcements.
                </p>

            </div>

            <a href="/communication/create"
                class="bg-red-500 text-white px-6 py-4 rounded-2xl font-semibold hover:bg-red-600 transition">

                + Create Notification

            </a>

        </div>

        <!-- SUMMARY CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">

            <div class="bg-white border rounded-3xl p-6">
                <p class="text-gray-400">Total Notifications</p>

                <h2 class="text-4xl font-black mt-2">
                    {{ $totalNotifications }}
                </h2>
            </div>

            <div class="bg-white border rounded-3xl p-6">
                <p class="text-gray-400">System Notifications</p>

                <h2 class="text-4xl font-black mt-2">
                    {{ $systemNotifications }}
                </h2>
            </div>

            <div class="bg-white border rounded-3xl p-6">
                <p class="text-gray-400">Manual Notifications</p>

                <h2 class="text-4xl font-black mt-2">
                    {{ $manualNotifications }}
                </h2>
            </div>

            <div class="bg-white border rounded-3xl p-6">
                <p class="text-gray-400">Delivered</p>

                <h2 class="text-4xl font-black mt-2">
                    {{ $deliveredNotifications }}
                </h2>
            </div>

        </div>

        <!-- TABLE -->
        <div class="bg-white border rounded-3xl overflow-hidden">

            <table class="w-full">

                <thead class="bg-red-50">

                    <tr>

                        <th class="text-left px-6 py-5">Date</th>

                        <th class="text-left px-6 py-5">Title</th>

                        <th class="text-left px-6 py-5">Type</th>

                        <th class="text-left px-6 py-5">Audience</th>

                        <th class="text-left px-6 py-5">Campaign</th>

                        <th class="text-left px-6 py-5">Recipients</th>

                        <th class="text-left px-6 py-5">Read</th>

                        <th class="text-left px-6 py-5">Status</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($notifications as $notification)

                        <tr class="border-t">

                            <!-- DATE -->
                            <td class="px-6 py-5">

                                {{ $notification->created_at->format('d M Y') }}

                            </td>

                            <!-- TITLE -->
                            <td class="px-6 py-5">

                                <p class="font-bold">
                                    {{ $notification->title }}
                                </p>

                                <p class="text-gray-500 text-sm mt-1">
                                    {{ $notification->message }}
                                </p>

                            </td>

                            <!-- TYPE -->
                            <td class="px-6 py-5">

                                @if($notification->type == 'System')

                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">

                                        System

                                    </span>

                                @else

                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold">

                                        Manual

                                    </span>

                                @endif

                            </td>

                            <!-- AUDIENCE -->
                            <td class="px-6 py-5">

                                {{ $notification->audience }}

                            </td>

                            <!-- CAMPAIGN -->
                            <td class="px-6 py-5">

                                {{ $notification->campaign->name ?? 'General Announcement' }}

                            </td>

                            <!-- RECIPIENTS -->
                            <td class="px-6 py-5">

                                {{ $notification->recipients ?? 0 }}

                            </td>
                            <!-- READ -->
                            <td class="px-6 py-5">

                                {{ $notification->read_count }}/{{ $notification->recipient_count }}
                                <!-- STATUS -->
                            <td class="px-6 py-5">

                                @if($notification->status == 'Delivered')

                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">

                                        Delivered

                                    </span>

                                @elseif($notification->status == 'Sent')

                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">

                                        Sent

                                    </span>

                                @elseif($notification->status == 'Failed')

                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">

                                        Failed

                                    </span>

                                @else

                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-semibold">

                                        Draft

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

@endsection