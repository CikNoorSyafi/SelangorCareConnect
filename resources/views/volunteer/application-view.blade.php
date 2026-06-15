@extends('layouts.volunteer')

@section('content')

    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-xl shadow p-10">

            <div class="text-center">

                <h1 class="text-4xl font-bold">
                    Application Details
                </h1>

                <p class="text-gray-500 mt-3">
                    View your submitted volunteer application.
                </p>

            </div>

            <hr class="my-8">

            <div class="grid md:grid-cols-2 gap-8">

                <div>

                    <h3 class="font-bold mb-4">
                        Application Information
                    </h3>

                    <div class="space-y-3">

                        <p>
                            <strong>Application ID:</strong>
                            {{ $application->id }}
                        </p>

                        <p>
                            <strong>Applied Date:</strong>
                            {{ $application->created_at?->format('d M Y h:i A') }}
                        </p>

                        <p>
                            <strong>Status:</strong>
                            {{ $application->status }}
                        </p>

                    </div>

                </div>
                <div class="bg-gray-50 rounded-lg p-6">

                    <h3 class="font-bold mb-4">
                        Campaign Information
                    </h3>

                    <p>
                        <strong>Campaign:</strong>
                        {{ $application->campaign->name ?? 'N/A' }}
                    </p>

                    <p>
                        <strong>Shift:</strong>
                        {{ $application->shift }}
                    </p>

                    <p>
                        <strong>Skill:</strong>
                        {{ $application->skill }}
                    </p>

                    <p>
                        <strong>Notes:</strong>
                        {{ $application->notes ?? '-' }}
                    </p>

                </div>

            </div>

            <div class="flex justify-center gap-4 mt-10">

                <a href="{{ route('volunteer.applications') }}" class="border px-6 py-3 rounded-lg">
                    Back to Applications
                </a>

                <a href="{{ route('volunteer.dashboard') }}" class="bg-red-500 text-white px-6 py-3 rounded-lg">
                    Dashboard
                </a>

            </div>

        </div>

    </div>

@endsection