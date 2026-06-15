@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Send Notification</h1>
        <p class="text-sm text-gray-500">Broadcast a system announcement to users (UC09).</p>
    </div>

    <div class="bg-white p-6 rounded-xl border shadow-sm max-w-2xl">
        <form method="POST" action="/admin/notifications/send" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="w-full border rounded px-3 py-2 text-sm" required>
                @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audience</label>
                <select name="audience" class="w-full border rounded px-3 py-2 text-sm" required>
                    <option value="All Volunteers">All Volunteers</option>
                    <option value="All Donors">All Donors</option>
                </select>
                @error('audience')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea name="message" rows="5"
                    class="w-full border rounded px-3 py-2 text-sm" required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button class="bg-primary text-white px-5 py-2 rounded text-sm font-semibold">
                Send Notification
            </button>
        </form>
    </div>

@endsection
