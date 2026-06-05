@extends('layouts.volunteer')

@section('content')

    <h1 class="text-3xl font-bold mb-6">
        My Applications
    </h1>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        @if(session('success'))

            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">

                {{ session('success') }}

            </div>

        @endif
        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>
                    <th class="p-4 text-left">ID</th>
                    <th class="p-4 text-left">Campaign</th>
                    <th class="p-4 text-left">Shift</th>
                    <th class="p-4 text-left">Status</th>
                    <th class="p-4 text-left">Date</th>
                    <th class="p-4 text-left">Actions</th>
                </tr>

            </thead>

            <tbody>

                @forelse($applications as $application)

                    <tr class="border-t">

                        <td class="p-4">
                            {{ $application['id'] }}
                        </td>

                        <td class="p-4">
                            {{ $application['campaign'] }}
                        </td>

                        <td class="p-4">
                            {{ $application['shift'] }}
                        </td>

                        <td class="p-4">

                            @if($application['status'] == 'Under Review')

                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">

                                    Under Review

                                </span>

                            @elseif($application['status'] == 'Approved')

                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                                    Approved

                                </span>

                            @elseif($application['status'] == 'Withdrawn')

                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">

                                    Withdrawn

                                </span>

                            @endif

                        </td>

                        <td class="p-4">
                            {{ $application['date'] }}
                        </td>

                        <td class="p-4">

                            @if($application['status'] != 'Withdrawn')

                                <a href="{{ route('volunteer.withdraw', $application['id']) }}"
                                    onclick="return confirm('Withdraw this application?')"
                                    class="bg-red-500 text-white px-3 py-2 rounded text-sm">

                                    Withdraw

                                </a>

                            @else

                                <span class="text-gray-400">
                                    N/A
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center p-8 text-gray-500">
                            No applications found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

@endsection