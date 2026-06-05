@extends('layouts.volunteer')

@section('content')

    <h1 class="text-3xl font-bold mb-6">
        My Profile
    </h1>

    <div class="bg-white rounded-xl shadow p-8 max-w-3xl">

        <div class="grid md:grid-cols-2 gap-6">

            <div>

                <label class="text-sm text-gray-500">
                    Full Name
                </label>

                <p class="font-semibold mt-1">
                    {{ $user['name'] }}
                </p>

            </div>

            <div>

                <label class="text-sm text-gray-500">
                    Email Address
                </label>

                <p class="font-semibold mt-1">
                    {{ $user['email'] }}
                </p>

            </div>

            <div>

                <label class="text-sm text-gray-500">
                    Role
                </label>

                <p class="font-semibold mt-1 capitalize">
                    {{ $user['role'] }}
                </p>

            </div>

            <div>

                <label class="text-sm text-gray-500">
                    Volunteer Level
                </label>

                <p class="font-semibold mt-1">
                    Level 1
                </p>

            </div>

        </div>

    </div>

@endsection