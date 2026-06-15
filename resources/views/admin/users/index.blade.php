@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Users</h1>
        <p class="text-sm text-gray-500">Update user roles and account status.</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="/admin/users" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search name or email"
            class="border rounded px-3 py-2 text-sm w-64">

        <select name="role" class="border rounded px-3 py-2 text-sm">
            <option value="">All Roles</option>
            <option value="organizer" {{ $role == 'organizer' ? 'selected' : '' }}>Organizer</option>
            <option value="volunteer" {{ $role == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
            <option value="donor" {{ $role == 'donor' ? 'selected' : '' }}>Donor</option>
            <option value="administrator" {{ $role == 'administrator' ? 'selected' : '' }}>Administrator</option>
        </select>

        <button class="bg-primary text-white px-4 py-2 rounded text-sm">Filter</button>
    </form>

    {{-- Users table --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-3 capitalize">{{ $user->role }}</td>
                        <td class="px-4 py-3">
                            @if ($user->is_active)
                                <span class="px-2 py-0.5 text-xs rounded bg-green-50 text-green-600">Active</span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-500">Suspended</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{-- Inline role update form --}}
                            <form method="POST" action="/admin/users/update-role/{{ $user->id }}"
                                class="flex items-center gap-2 justify-end">
                                @csrf
                                <select name="role" class="border rounded px-2 py-1 text-xs">
                                    <option value="organizer" {{ $user->role == 'organizer' ? 'selected' : '' }}>Organizer</option>
                                    <option value="volunteer" {{ $user->role == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                                    <option value="donor" {{ $user->role == 'donor' ? 'selected' : '' }}>Donor</option>
                                    <option value="administrator" {{ $user->role == 'administrator' ? 'selected' : '' }}>Administrator</option>
                                </select>

                                <label class="flex items-center gap-1 text-xs text-gray-500">
                                    <input type="checkbox" name="is_active" {{ $user->is_active ? 'checked' : '' }}>
                                    Active
                                </label>

                                <button class="bg-primary text-white px-3 py-1 rounded text-xs">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

@endsection
