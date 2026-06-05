@extends('layouts.organizer')

@section('content')

    <div class="bg-white p-6 rounded shadow">
        @if(session('success'))

            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">

                {{ session('success') }}

            </div>

        @endif

        @if(session('error'))

            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">

                {{ session('error') }}

            </div>

        @endif
        <h1 class="text-2xl font-bold mb-6">
            Edit Volunteer
        </h1>

        <form method="POST" action="/volunteers/update/{{ $volunteer->id }}">

            @csrf

            <input type="hidden" name="volunteer_id" value="{{ $volunteer->id }}">

            <div class="grid grid-cols-2 gap-4 mb-8">

                <div>

                    <label class="font-semibold">
                        Name
                    </label>

                    <p>{{ $volunteer->name }}</p>

                </div>

                <div>

                    <label class="font-semibold">
                        Email
                    </label>

                    <p>{{ $volunteer->email }}</p>

                </div>

                <div>

                    <label class="font-semibold">
                        Role
                    </label>

                    <p>{{ $volunteer->role }}</p>

                </div>

                <div>

                    <label class="font-semibold">
                        Joined Date
                    </label>

                    <p>

                        {{ $volunteer->created_at
        ? $volunteer->created_at->format('d M Y')
        : '-' }}

                    </p>

                </div>

            </div>

            {{-- SKILLS --}}

            <div class="mb-8">

                <label class="font-semibold block mb-3">

                    Skills

                </label>

                <div class="flex flex-wrap gap-2 mb-4">

                    @forelse($volunteer->volunteerSkills as $vs)

                        <div class="flex items-center gap-2 bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm">

                            <span>

                                {{ $vs->skill->name }}

                            </span>

                            <a href="/volunteers/skills/delete/{{ $vs->id }}" onclick="return confirm('Remove this skill?')"
                                class="font-bold">

                                ✕

                            </a>

                        </div>

                    @empty

                        <span class="text-gray-400">

                            No skills assigned

                        </span>

                    @endforelse

                </div>

                <div class="flex gap-2">

                    <select name="new_skill_id" class="border p-2 rounded">

                        <option value="">
                            -- Select Skill --
                        </option>

                        @foreach($skills as $skill)

                            <option value="{{ $skill->id }}">

                                {{ $skill->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            {{-- CAMPAIGN ASSIGNMENTS --}}

            <h2 class="text-xl font-bold mb-4">

                Campaign Assignments

            </h2>

            <table class="w-full border">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="p-3 text-left">
                            Campaign
                        </th>

                        <th class="p-3 text-left">
                            Role
                        </th>

                        <th class="p-3 text-left">
                            Shift
                        </th>

                        <th class="p-3 text-left">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($assignments as $assignment)

                        <tr class="border-t">

                            <td class="p-3">

                                {{ $assignment->campaign->name }}

                            </td>

                            <td class="p-3">

                                <select name="role_id[{{ $assignment->id }}]" class="border p-2 rounded w-full">

                                    @foreach($roles as $role)

                                        <option value="{{ $role->id }}" {{ $assignment->role_id == $role->id ? 'selected' : '' }}>

                                            {{ $role->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </td>

                            <td class="p-3">

                                <select name="shift_id[{{ $assignment->id }}]" class="border p-2 rounded w-full">

                                    @foreach($shifts as $shift)

                                        <option value="{{ $shift->id }}" {{ $assignment->shift_id == $shift->id ? 'selected' : '' }}>

                                            {{ $shift->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </td>
                            <td class="p-3">

                                <a href="/volunteers/assignment/delete/{{ $assignment->id }}"
                                    onclick="return confirm('Remove this campaign assignment?')"
                                    class="bg-red-500 text-white px-3 py-2 rounded">

                                    Remove

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td class="p-3">

                                <select name="campaign_id" class="border p-2 rounded w-full">

                                    <option value="">

                                        Select Campaign

                                    </option>

                                    @foreach($campaigns as $campaign)

                                        <option value="{{ $campaign->id }}">

                                            {{ $campaign->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </td>

                            <td class="p-3">

                                <select name="new_role_id" class="border p-2 rounded w-full">

                                    @foreach($roles as $role)

                                        <option value="{{ $role->id }}">

                                            {{ $role->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </td>

                            <td class="p-3">

                                <select name="new_shift_id" class="border p-2 rounded w-full">

                                    @foreach($shifts as $shift)

                                        <option value="{{ $shift->id }}">

                                            {{ $shift->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

            <h3 class="text-lg font-semibold mt-6 mb-3">

                Add New Campaign Assignment

            </h3>

            <div class="grid grid-cols-3 gap-4 mb-4">

                <select name="new_campaign_id" class="border p-2 rounded">

                    <option value="">
                        Select Campaign
                    </option>

                    @foreach($campaigns as $campaign)

                        <option value="{{ $campaign->id }}">

                            {{ $campaign->name }}

                        </option>

                    @endforeach

                </select>

                <select name="new_role_id" class="border p-2 rounded">

                    <option value="">
                        Select Role
                    </option>

                    @foreach($roles as $role)

                        <option value="{{ $role->id }}">

                            {{ $role->name }}

                        </option>

                    @endforeach

                </select>

                <select name="new_shift_id" class="border p-2 rounded">

                    <option value="">
                        Select Shift
                    </option>

                    @foreach($shifts as $shift)

                        <option value="{{ $shift->id }}">

                            {{ $shift->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mt-6 flex gap-3">

                <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded">

                    Save Changes

                </button>

                <a href="/volunteers" class="border px-6 py-2 rounded">

                    Cancel

                </a>

            </div>

        </form>

    </div>

@endsection