@extends(
    session('user.role') == 'organizer'
        ? 'layouts.organizer'
        : (
            session('user.role') == 'volunteer'
                ? 'layouts.volunteer'
                : 'layouts.donor'
        )
)

@section('content')

<div class="max-w-5xl mx-auto">

    <h1 class="text-4xl font-bold mb-8">
        Notifications
    </h1>

    @if($notifications->count() == 0)

        <div class="bg-white border rounded-2xl p-8 text-center text-gray-500">
            No notifications available.
        </div>

    @endif

    @foreach($notifications as $item)

        <div class="bg-white border rounded-2xl p-6 mb-5 shadow-sm">

            <div class="flex justify-between items-start">

                <div>

                    <h3 class="font-bold text-lg text-gray-900">
                        {{ $item->notification->title }}
                    </h3>

                    <p class="text-sm text-gray-400 mt-1">
                        {{ $item->notification->created_at->format('d M Y H:i') }}
                    </p>

                </div>

                @if($item->is_read)

                    <span
                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                        Read
                    </span>

                @else

                    <span
                        class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
                        New
                    </span>

                @endif

            </div>

            <div class="border-t mt-4 pt-4">

                <p class="text-gray-700 leading-relaxed">
                    {{ $item->notification->message }}
                </p>

            </div>

            <div class="mt-4 text-sm text-gray-500 space-y-1">

                <div>
                    <strong>Type:</strong>
                    {{ $item->notification->type }}
                </div>

                <div>
                    <strong>Status:</strong>
                    {{ $item->notification->status }}
                </div>

                <div>
                    <strong>Recipients:</strong>
                    {{ $item->notification->recipients }}
                </div>

            </div>

            @if(!$item->is_read)

                <div class="mt-5">

                    <form method="POST"
                        action="/notifications/{{ $item->id }}/read">

                        @csrf

                        <button
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl">

                            Mark As Read

                        </button>

                    </form>

                </div>

            @endif

        </div>

    @endforeach

</div>

@endsection