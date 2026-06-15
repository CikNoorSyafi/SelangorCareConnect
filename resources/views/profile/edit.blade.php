@extends('layouts.' . session('user.role'))

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

                    <input type="text" name="name" value="{{ $user->name }}" class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="block mb-2 font-semibold">
                        Email Address
                    </label>

                    <input type="email" name="email" value="{{ $user->email }}" class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="block mb-2 font-semibold">
                        Phone Number
                    </label>

                    <input type="text" name="phone" value="{{ $user->phone }}" class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="block mb-2 font-semibold">
                        Role
                    </label>

                    <input type="text" value="{{ ucfirst($user->role) }}" readonly
                        class="w-full border rounded-2xl px-5 py-4 bg-gray-100 text-gray-500 cursor-not-allowed">
                </div>

            </div>

            @if(session('user.role') == 'organizer')

                <div class="mt-6">

                    <label class="block mb-2 font-semibold">
                        Organization
                    </label>

                    <input type="text" name="organization" value="{{ $user->organization }}"
                        class="w-full border rounded-2xl px-5 py-4">

                </div>

            @endif

            <div class="mt-8">

                <h3 class="font-bold text-xl mb-4">
                    Notification Preferences
                </h3>

                {{-- ORGANIZER --}}
                @if(session('user.role') == 'organizer')

                    <div class="space-y-3">

                        <label class="flex items-center gap-3">

                            <input type="checkbox" name="campaign_notifications" {{ $user->campaign_notifications ? 'checked' : '' }}>

                            <span>
                                Campaign Notifications
                            </span>

                        </label>

                        <label class="flex items-center gap-3">

                            <input type="checkbox" name="volunteer_notifications" {{ $user->volunteer_notifications ? 'checked' : '' }}>

                            <span>
                                Volunteer Notifications
                            </span>

                        </label>

                        <label class="flex items-center gap-3">

                            <input type="checkbox" name="communication_notifications" {{ $user->communication_notifications ? 'checked' : '' }}>

                            <span>
                                Communication Notifications
                            </span>

                        </label>

                    </div>

                @endif


                {{-- DONOR --}}
                @if(session('user.role') == 'donor')

                    <div class="space-y-3">

                        <label class="flex items-center gap-3">

                            <input type="checkbox" name="donation_notifications" {{ $user->donation_notifications ? 'checked' : '' }}>

                            <span>
                                Donation Notifications
                            </span>

                        </label>

                        <label class="flex items-center gap-3">

                            <input type="checkbox" name="communication_notifications" {{ $user->communication_notifications ? 'checked' : '' }}>

                            <span>
                                Communication Notifications
                            </span>

                        </label>

                    </div>

                @endif


                {{-- VOLUNTEER --}}
                @if(session('user.role') == 'volunteer')

                    <div class="space-y-3">

                        <label class="flex items-center gap-3">

                            <input type="checkbox" name="campaign_notifications" {{ $user->campaign_notifications ? 'checked' : '' }}>

                            <span>
                                Campaign Notifications
                            </span>

                        </label>

                        <label class="flex items-center gap-3">

                            <input type="checkbox" name="volunteer_notifications" {{ $user->volunteer_notifications ? 'checked' : '' }}>

                            <span>
                                Volunteer Notifications
                            </span>

                        </label>

                    </div>

                @endif

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