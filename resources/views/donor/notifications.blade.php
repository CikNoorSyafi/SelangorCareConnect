@extends('layouts.donor')

@section('content')

    <div class="max-w-7xl mx-auto">

        <div class="mb-10">

            <h1 class="text-5xl font-black text-gray-900">
                Notifications
            </h1>

            <p class="text-gray-500 text-xl mt-3">
                View announcements and updates.
            </p>

        </div>

        <div class="bg-white border rounded-3xl overflow-hidden">

            <table class="w-full">

                <thead class="bg-red-50">

                    <tr>

                        <th class="text-left px-6 py-5">
                            Date
                        </th>

                        <th class="text-left px-6 py-5">
                            Title
                        </th>

                        <th class="text-left px-6 py-5">
                            Message
                        </th>

                        <th class="text-left px-6 py-5">
                            Status
                        </th>

                        <th class="text-left px-6 py-5">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @if($notifications->count() > 0)

                        @foreach($notifications as $item)

                            <tr class="border-t">

                                <td class="px-6 py-5">
                                    {{ $item->created_at->format('d M Y') }}
                                </td>

                                <td class="px-6 py-5 font-semibold">
                                    {{ $item->notification->title }}
                                </td>

                                <td class="px-6 py-5 text-gray-600">
                                    {{ \Illuminate\Support\Str::limit($item->notification->message, 50) }}
                                </td>

                                <td class="px-6 py-5">

                                    @if($item->is_read)

                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                                            Read
                                        </span>

                                    @else

                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                                            Unread
                                        </span>

                                    @endif

                                </td>

                                <td class="px-6 py-5">

                                    <button onclick='openNotification(
                                                        {{ $item->id }},
                                                        @json($item->notification->title),
                                                        @json($item->notification->message)
                                                    )'
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0
                                                                                                3 3 0 016 0zm7.938 0
                                                                                                a10.97 10.97 0 01-1.34 2.75
                                                                                                C19.945 17.44 16.315 20
                                                                                                12 20s-7.945-2.56-9.598-5.25
                                                                                                A10.97 10.97 0 011.062 12
                                                                                                c.218-.96.67-1.88 1.34-2.75
                                                                                                C4.055 6.56 7.685 4 12 4
                                                                                                s7.945 2.56 9.598 5.25
                                                                                                c.67.87 1.122 1.79 1.34 2.75z" />

                                        </svg>

                                    </button>

                                </td>

                            </tr>

                        @endforeach

                    @else

                        <tr>

                            <td colspan="5" class="text-center py-10 text-gray-500">

                                No notifications available.

                            </td>

                        </tr>

                    @endif

                </tbody>

            </table>

        </div>

    </div>

    <div id="notificationModal" class="fixed inset-0 bg-black/50 hidden
                                            items-center justify-center z-50">

        <div class="bg-white rounded-3xl p-8 max-w-xl w-full">

            <h2 id="modalTitle" class="text-2xl font-bold mb-4">
            </h2>

            <p id="modalMessage" class="text-gray-600 leading-relaxed">
            </p>

            <div class="mt-6 text-right">

                <button onclick="closeNotification()" class="bg-red-500 text-white
                                                    px-5 py-2 rounded-xl">

                    Close

                </button>

            </div>

        </div>

    </div>

    <script>

        function openNotification(id, title, message) {
            document.getElementById('modalTitle').innerText = title;

            document.getElementById('modalMessage').innerText = message;

            document.getElementById('notificationModal')
                .classList.remove('hidden');

            document.getElementById('notificationModal')
                .classList.add('flex');

            fetch(
                '/donor/notifications/' + id + '/read',
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':
                            '{{ csrf_token() }}',
                        'Content-Type':
                            'application/json'
                    }
                }
            );
        }

        function closeNotification() {
            document.getElementById('notificationModal')
                .classList.add('hidden');

            document.getElementById('notificationModal')
                .classList.remove('flex');

            location.reload();
        }

    </script>


@endsection