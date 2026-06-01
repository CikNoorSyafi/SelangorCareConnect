@extends('layouts.organizer')

@section('content')

    <div class="max-w-4xl mx-auto bg-white border rounded-2xl p-8">

        <!-- HEADER -->
        <div class="mb-8">

            <h1 class="text-4xl font-black text-gray-900">
                Edit Donation
            </h1>

            <p class="text-gray-500 mt-2">
                Update donation information.
            </p>

        </div>

        <!-- FORM -->
        <form action="/donation/update/{{ $donation['id'] }}" method="POST">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- CONTRIBUTOR -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Contributor Name
                    </label>

                    <input type="text" name="contributor" value="{{ $donation['contributor'] }}" required
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                </div>

                <!-- CAMPAIGN -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Campaign
                    </label>

                    <input type="text" name="campaign" value="{{ $donation['campaign'] }}" required
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                </div>

                <!-- AMOUNT -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Amount (RM)
                    </label>

                    <input type="number" step="0.01" name="amount" value="{{ $donation['amount'] }}" required
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                </div>

                <!-- STATUS -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Status
                    </label>

                    <select name="status"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                        <option value="Allocated" {{ $donation['status'] == 'Allocated' ? 'selected' : '' }}>

                            Allocated

                        </option>

                        <option value="Pending" {{ $donation['status'] == 'Pending' ? 'selected' : '' }}>

                            Pending

                        </option>

                    </select>

                </div>

            </div>

            <!-- BUTTON -->
            <div class="flex gap-4 mt-8">

                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-yellow-500 text-white font-semibold hover:bg-yellow-600 transition">

                    Update Donation

                </button>

                <a href="/donation" class="px-6 py-3 rounded-xl border hover:bg-gray-50 transition">

                    Cancel

                </a>

            </div>

        </form>

    </div>

@endsection