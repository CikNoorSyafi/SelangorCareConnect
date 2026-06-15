@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Verify Organizations</h1>
        <p class="text-sm text-gray-500">Review and verify organizer / NGO organization details.</p>
    </div>

    {{-- Status filter --}}
    <form method="GET" action="/admin/organizations" class="flex gap-3 mb-5">
        <select name="status" class="border rounded px-3 py-2 text-sm">
            <option value="">All Statuses</option>
            <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Verified" {{ $status == 'Verified' ? 'selected' : '' }}>Verified</option>
            <option value="Rejected" {{ $status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button class="bg-primary text-white px-4 py-2 rounded text-sm">Filter</button>
    </form>

    <div class="space-y-4">
        @forelse ($organizations as $org)
            <div class="bg-white rounded-xl border shadow-sm p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="font-semibold text-gray-800">
                            {{ $org->organization ?? 'No organization name' }}
                        </h2>
                        <p class="text-sm text-gray-500">{{ $org->name }} &middot; {{ $org->email }}</p>
                        <p class="text-sm text-gray-500">{{ $org->phone ?? 'No phone' }}</p>
                    </div>

                    @php
                        $badge = match ($org->verification_status) {
                            'Verified' => 'bg-green-50 text-green-600',
                            'Rejected' => 'bg-red-50 text-red-600',
                            default => 'bg-yellow-50 text-yellow-700',
                        };
                    @endphp
                    <span class="px-3 py-1 text-xs rounded {{ $badge }}">
                        {{ $org->verification_status }}
                    </span>
                </div>

                @if ($org->verification_note)
                    <p class="text-xs text-gray-400 mt-2">Note: {{ $org->verification_note }}</p>
                @endif

                {{-- Verification action form --}}
                <form method="POST" action="/admin/organizations/verify/{{ $org->id }}"
                    class="mt-4 flex flex-wrap items-center gap-3 border-t pt-4">
                    @csrf

                    <select name="verification_status" class="border rounded px-3 py-2 text-sm">
                        <option value="Pending" {{ $org->verification_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Verified" {{ $org->verification_status == 'Verified' ? 'selected' : '' }}>Verify</option>
                        <option value="Rejected" {{ $org->verification_status == 'Rejected' ? 'selected' : '' }}>Reject</option>
                    </select>

                    <input type="text" name="verification_note" value="{{ $org->verification_note }}"
                        placeholder="Optional note" class="border rounded px-3 py-2 text-sm flex-1 min-w-[200px]">

                    <button class="bg-primary text-white px-4 py-2 rounded text-sm">Update</button>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-xl border shadow-sm p-6 text-center text-gray-400">
                No organizations found.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $organizations->links() }}
    </div>

@endsection
