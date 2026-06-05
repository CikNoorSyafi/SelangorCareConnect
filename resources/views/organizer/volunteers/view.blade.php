@extends('layouts.organizer')

@section('content')

    <div class="bg-white p-6 rounded shadow">

        <h1 class="text-2xl font-bold mb-6">
            Volunteer Profile
        </h1>

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
                <div>

                    <label class="font-semibold">
                        Skills
                    </label>

                    <div class="flex flex-wrap gap-2 mt-2">

                        @forelse($volunteer->volunteerSkills as $vs)

                            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm">

                                <div class="flex items-center gap-2">

                                    <span>

                                        {{ $vs->skill->name }}

                                    </span>

                                </div>

                            </span>

                        @empty

                            <span class="text-gray-400">

                                No skills assigned

                            </span>

                        @endforelse

                    </div>
                </div>

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

                </tr>

            </thead>

            <tbody>

                @forelse($assignments as $assignment)

                    <tr class="border-t">

                        <td class="p-3">

                            {{ $assignment->campaign->name ?? '-' }}

                        </td>

                        <td class="p-3">

                            {{ $assignment->role->name ?? '-' }}

                        </td>

                        <td class="p-3">

                            {{ $assignment->shift->name ?? '-' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3" class="p-4 text-center text-gray-500">

                            No campaign assigned

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        <a href="/volunteers" class="inline-block mt-6 border px-4 py-2 rounded">

            Back

        </a>

    </div>

@endsection