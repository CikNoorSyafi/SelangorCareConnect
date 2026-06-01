@extends('layouts.organizer')

@section('content')

    <div class="max-w-5xl mx-auto">

        <div class="flex justify-between items-start mb-10">

            <div>

                <h1 class="text-5xl font-black">
                    Edit Profile
                </h1>

                <p class="text-gray-500 mt-3 text-lg">
                    Update your account information.
                </p>

            </div>

            <a href="/profile" class="px-6 py-4 border rounded-2xl hover:bg-gray-50">
                Back
            </a>

        </div>

        <form action="/profile/update" method="POST" class="bg-white border rounded-3xl p-8">

            @csrf

            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <label class="block mb-2 font-semibold">
                        Full Name
                    </label>

                    <input type="text" name="name" value="{{ $profile['name'] }}"
                        class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="block mb-2 font-semibold">
                        Email Address
                    </label>

                    <input type="email" name="email" value="{{ $profile['email'] }}"
                        class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="block mb-2 font-semibold">
                        Phone Number
                    </label>

                    <input type="text" name="phone" value="{{ $profile['phone'] }}"
                        class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="block mb-2 font-semibold">
                        Role
                    </label>

                    <input type="text" name="role" value="{{ $profile['role'] }}"
                        class="w-full border rounded-2xl px-5 py-4">
                </div>

            </div>

            <div class="mt-6">

                <label class="block mb-2 font-semibold">
                    Organization
                </label>

                <input type="text" name="organization" value="{{ $profile['organization'] }}"
                    class="w-full border rounded-2xl px-5 py-4">

            </div>

            <div class="mt-8">

                <h3 class="font-bold text-xl mb-4">
                    Notification Preferences
                </h3>

                <div class="space-y-4">

                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="campaign_notifications" {{ $profile['campaign_notifications'] ? 'checked' : '' }}>
                        Campaign Notifications
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="volunteer_notifications" {{ $profile['volunteer_notifications'] ? 'checked' : '' }}>
                        Volunteer Notifications
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="donation_notifications" {{ $profile['donation_notifications'] ? 'checked' : '' }}>
                        Donation Notifications
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="communication_notifications" {{ $profile['communication_notifications'] ? 'checked' : '' }}>
                        Communication Notifications
                    </label>

                </div>

            </div>

            <div class="flex gap-4 mt-10">

                <button type="submit" class="bg-red-500 text-white px-8 py-4 rounded-2xl hover:bg-red-600">
                    Save Changes
                </button>

                <a href="/profile" class="px-8 py-4 border rounded-2xl hover:bg-gray-50">
                    Cancel
                </a>

            </div>

        </form>

    </div>

@endsection