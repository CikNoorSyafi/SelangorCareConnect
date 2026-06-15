@extends('layouts.' . session('user.role'))

@section('content')

    <div class="max-w-5xl mx-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-10">

            <div>

                <h1 class="text-5xl font-black text-gray-900">
                    Change Password
                </h1>

                <p class="text-gray-500 text-lg mt-3">
                    Update your account password.
                </p>

            </div>

            <a href="/profile" class="px-6 py-4 border rounded-2xl hover:bg-gray-50 transition">
                Back
            </a>

        </div>

        <!-- PASSWORD FORM -->
        <form action="/profile/password" method="POST" class="bg-white border rounded-3xl p-8">

            @csrf

            <div class="space-y-6">

                <!-- CURRENT PASSWORD -->
                <div>

                    <label class="block mb-2 font-semibold">
                        Current Password
                    </label>

                    <input type="password" name="current_password" placeholder="Enter current password"
                        class="w-full border rounded-2xl px-5 py-4">

                </div>

                <!-- NEW PASSWORD -->
                <div>

                    <label class="block mb-2 font-semibold">
                        New Password
                    </label>

                    <input type="password" name="new_password" placeholder="Enter new password"
                        class="w-full border rounded-2xl px-5 py-4">

                </div>

                <!-- CONFIRM PASSWORD -->
                <div>

                    <label class="block mb-2 font-semibold">
                        Confirm New Password
                    </label>

                    <input type="password" name="confirm_password" placeholder="Confirm new password"
                        class="w-full border rounded-2xl px-5 py-4">

                </div>

            </div>

            <!-- BUTTONS -->
            <div class="flex gap-4 mt-10">

                <button type="submit"
                    class="bg-red-500 text-white px-8 py-4 rounded-2xl font-semibold hover:bg-red-600 transition">

                    Update Password

                </button>

                <a href="/profile" class="px-8 py-4 border rounded-2xl hover:bg-gray-50 transition">

                    Cancel

                </a>

            </div>

        </form>

    </div>

@endsection