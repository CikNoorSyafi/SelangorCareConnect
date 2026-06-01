@extends('layouts.organizer')

@section('content')

    <div class="max-w-6xl mx-auto">

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-10">

            <div>
                <h1 class="text-5xl font-black text-gray-900">
                    My Profile
                </h1>

                <p class="text-gray-500 mt-3 text-lg">
                    Manage your account information and preferences.
                </p>
            </div>

            <a href="/profile/edit" class="px-6 py-4 border rounded-2xl hover:bg-gray-50 transition">
                Edit Profile
            </a>

        </div>

        <!-- PROFILE CARD -->
        <div class="bg-white border rounded-3xl p-8">

            <div class="grid md:grid-cols-2 gap-8">

                <div>
                    <p class="text-gray-400 mb-1">Full Name</p>
                    <h3 class="text-xl font-bold">
                        {{ $profile['name'] }}
                    </h3>
                </div>

                <div>
                    <p class="text-gray-400 mb-1">Email Address</p>
                    <h3 class="text-xl font-bold">
                        {{ $profile['email'] }}
                    </h3>
                </div>

                <div>
                    <p class="text-gray-400 mb-1">Phone Number</p>
                    <h3 class="text-xl font-bold">
                        {{ $profile['phone'] }}
                    </h3>
                </div>

                <div>
                    <p class="text-gray-400 mb-1">Role</p>
                    <h3 class="text-xl font-bold">
                        {{ $profile['role'] }}
                    </h3>
                </div>

                <div>
                    <p class="text-gray-400 mb-1">Organization</p>
                    <h3 class="text-xl font-bold">
                        {{ $profile['organization'] }}
                    </h3>
                </div>

            </div>

        </div>

        <!-- NOTIFICATION SETTINGS -->
        <div class="bg-white border rounded-3xl p-8 mt-8">

            <h2 class="text-2xl font-bold mb-6">
                Notification Preferences
            </h2>

            <div class="space-y-4">

                <div class="flex justify-between">
                    <span>Campaign Notifications</span>
                    <span>
                        {{ $profile['campaign_notifications'] ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Volunteer Notifications</span>
                    <span>
                        {{ $profile['volunteer_notifications'] ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Donation Notifications</span>
                    <span>
                        {{ $profile['donation_notifications'] ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Communication Notifications</span>
                    <span>
                        {{ $profile['communication_notifications'] ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

            </div>

        </div>

        <!-- SECURITY -->
        <div class="bg-white border rounded-3xl p-8 mt-8">

            <h2 class="text-2xl font-bold mb-4">
                Security
            </h2>

            <a href="/profile/password" class="inline-flex px-6 py-3 bg-red-500 text-white rounded-2xl hover:bg-red-600">
                Change Password
            </a>

        </div>

    </div>

@endsection