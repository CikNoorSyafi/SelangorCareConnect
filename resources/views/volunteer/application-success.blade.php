@extends('layouts.volunteer')

@section('content')

    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-xl shadow p-10">

            <div class="text-center">

                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto">

                    <span class="text-5xl text-green-600">
                        ✓
                    </span>

                </div>

                <h1 class="text-4xl font-bold mt-6">
                    Application Submitted Successfully!
                </h1>

                <p class="text-gray-500 mt-3">
                    Thank you for volunteering.
                </p>

            </div>

            <hr class="my-8">

            <div class="grid md:grid-cols-2 gap-8">

                <div>

                    <h3 class="font-bold mb-4">
                        Application Status
                    </h3>

                    <div class="space-y-4">

                        <div>
                            <span class="font-semibold text-green-600">
                                Submitted
                            </span>

                            <p class="text-sm text-gray-500">
                                {{ $application->created_at?->format('d M Y h:i A') }}
                            </p>
                        </div>

                        <div>
                            <span class="font-semibold text-yellow-600">
                                Under Review
                            </span>

                            <p class="text-sm text-gray-500">
                                Pending organizer approval
                            </p>
                        </div>

                    </div>

                </div>
                <div class="bg-gray-50 rounded-lg p-6">

                    <h3 class="font-bold mb-4">
                        Application Summary
                    </h3>

                    <p>
                        <strong>ID:</strong>
                        {{ $application->id }}
                    </p>

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
                        <strong>Status:</strong>
                        {{ $application->status }}
                    </p>

                </div>

            </div>

            <div class="flex justify-center gap-4 mt-10">

                <a href="{{ route('volunteer.dashboard') }}" class="border px-6 py-3 rounded-lg">

                    Return to Dashboard

                </a>

                <a href="{{ route('volunteer.applications') }}" class="bg-red-500 text-white px-6 py-3 rounded-lg">

                    View My Applications

                </a>

            </div>

        </div>

    </div>

@endsection