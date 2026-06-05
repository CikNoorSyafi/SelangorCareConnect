@extends('layouts.volunteer')

@section('content')

    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-xl shadow p-8">

            <h1 class="text-3xl font-bold mb-2">
                Volunteer Application
            </h1>

            <p class="text-gray-500 mb-6">
                {{ $campaign['title'] }}
            </p>

            <form method="POST" action="{{ route('volunteer.apply') }}">

                @csrf

                <input type="hidden" name="campaign" value="{{ $campaign['title'] }}">

                <div class="mb-6">

                    <label class="block font-semibold mb-2">
                        Preferred Shift
                    </label>

                    <div class="grid md:grid-cols-2 gap-4">

                        @foreach($campaign['shifts'] as $shift)

                            <label class="border rounded-lg p-4 cursor-pointer">

                                <input type="radio" name="shift" value="{{ $shift['name'] }}" required>

                                <div class="mt-2">

                                    <div class="font-semibold">
                                        {{ $shift['name'] }}
                                    </div>

                                    <div class="text-sm text-gray-500">
                                        {{ $shift['time'] }}
                                    </div>

                                    <div class="text-green-600 text-sm">
                                        {{ $shift['remaining'] }} slots remaining
                                    </div>

                                </div>

                            </label>

                        @endforeach

                    </div>

                </div>
                <div class="mt-4">

                    <label class="block font-semibold mb-2">
                        Relevant Skill
                    </label>

                    <select name="skill" class="w-full border rounded-lg p-3">

                        <option value="">
                            Select Skill
                        </option>

                        @foreach($campaign['skills'] as $skill)

                            <option value="{{ $skill }}">
                                {{ $skill }}
                            </option>

                        @endforeach

                    </select>

                </div>
                <div class="mb-6">

                    <label class="block font-semibold mb-2">
                        Skills & Notes
                    </label>

                    <textarea name="notes" rows="4" class="w-full border rounded-lg p-3"></textarea>

                </div>

                <div class="mb-6">

                    <label class="flex items-center gap-2">

                        <input type="checkbox" required>

                        I agree to the Terms of Volunteering

                    </label>

                </div>

                <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-lg">

                    Submit Application

                </button>

            </form>

        </div>

    </div>

@endsection