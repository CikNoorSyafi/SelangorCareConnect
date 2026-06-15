@extends('layouts.volunteer')

@section('content')

    <div class="flex justify-between items-center mb-8">

        <div>
            <h1 class="text-5xl font-extrabold">
                My Applications
            </h1>

            <p class="text-gray-500 mt-2">
                View and manage your volunteer applications.
            </p>
        </div>

        <a href="{{ route('volunteer.dashboard') }}"
            class="px-8 py-4 border rounded-3xl bg-white hover:bg-gray-50 shadow-sm">

            Back to Dashboard

        </a>

    </div>

    <table class="w-full">

        <thead class="bg-gray-100">

            <tr>
                <th class="p-4 text-left">Campaign</th>
                <th class="p-4 text-left">Applied Date</th>
                <th class="p-4 text-left">Shift</th>
                <th class="p-4 text-left">Skill</th>
                <th class="p-4 text-left">Status</th>
                <th class="p-4 text-left">Action</th>
            </tr>

        </thead>

        <tbody>

            @forelse($applications as $application)

                <tr class="border-t">

                    <td class="p-4">
                        {{ $application->campaign->name ?? 'N/A' }}
                    </td>

                    <td class="p-4">
                        {{ $application->created_at?->format('d M Y h:i A') }}
                    </td>

                    <td class="p-4">
                        {{ $application->shift }}
                    </td>

                    <td class="p-4">
                        {{ $application->skill }}
                    </td>

                    {{-- STATUS --}}
                    <td class="p-4">

                        @if($application->status == 'Pending')

                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">
                                Pending
                            </span>

                        @elseif($application->status == 'Approved')

                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                                Approved
                            </span>

                        @elseif($application->status == 'Assigned')

                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                Assigned
                            </span>

                        @elseif($application->status == 'Withdrawn')

                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                Withdrawn
                            </span>

                        @else

                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                                {{ $application->status }}
                            </span>

                        @endif

                    </td>

                    {{-- ACTION --}}
                    <td class="p-4">

                        <div class="flex items-center gap-4">

                            <a href="{{ route('volunteer.application.view', $application->id) }}"
                                class="text-blue-600 hover:underline">

                                View

                            </a>

                            @if($application->status == 'Pending')

                                <form action="{{ route('volunteer.withdraw', $application->id) }}" method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to withdraw this application?')"
                                        class="text-red-600 hover:underline">

                                        Withdraw

                                    </button>

                                </form>

                            @endif

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" class="p-6 text-center text-gray-500">

                        No applications found.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    </div>

@endsection