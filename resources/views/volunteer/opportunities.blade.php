@extends('layouts.volunteer')

@section('content')

    <h1 class="text-3xl font-bold mb-6">
        Volunteer Opportunities
    </h1>

    <div class="bg-white p-6 rounded shadow mb-6">

        <h3 class="font-semibold">
            My Active Applications
        </h3>

        <p class="text-red-500">
            {{ count($applications ?? []) }} Active Applications
        </p>

    </div>

    <div class="grid md:grid-cols-3 gap-6">

        @foreach($campaigns as $campaign)

            <div class="bg-white rounded shadow p-5">

                <h3 class="font-bold mb-2">
                    {{ $campaign['title'] }}
                </h3>

                <p>{{ $campaign['category'] }}</p>

                <p>{{ $campaign['location'] }}</p>

                <p>{{ $campaign['date'] }}</p>

                <a href="{{ route('volunteer.application', $campaign['id']) }}"
                    class="block text-center mt-4 w-full bg-red-500 text-white py-2 rounded">

                    Apply Now

                </a>

            </div>

        @endforeach

    </div>

@endsection