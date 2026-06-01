@extends('layouts.organizer')

@section('content')

<div class="max-w-5xl mx-auto">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Edit Volunteer
            </h1>

            <p class="text-gray-500 mt-1">
                Update volunteer information and participation details.
            </p>
        </div>

        <a href="/volunteers"
           class="border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100">
            Back
        </a>

    </div>

    <!-- FORM CARD -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        <form action="/volunteers/update/{{ $volunteer['id'] }}" method="POST">
            @csrf

            <!-- PERSONAL INFO -->
            <div class="mb-8">

                <h2 class="text-lg font-semibold mb-4 text-gray-700">
                    Personal Information
                </h2>

                <div class="grid grid-cols-2 gap-5">

                    <!-- NAME -->
                    <div>
                        <label class="text-sm text-gray-600 mb-1 block">
                            Full Name
                        </label>

                        <input type="text"
                               name="name"
                               value="{{ $volunteer['name'] }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>

                    <!-- EMAIL -->
                    <div>
                        <label class="text-sm text-gray-600 mb-1 block">
                            Email Address
                        </label>

                        <input type="email"
                               name="email"
                               value="{{ $volunteer['email'] }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>

                    <!-- PHONE -->
                    <div>
                        <label class="text-sm text-gray-600 mb-1 block">
                            Phone Number
                        </label>

                        <input type="text"
                               name="phone"
                               value="{{ $volunteer['phone'] ?? '012-3456789' }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>

                    <!-- AGE -->
                    <div>
                        <label class="text-sm text-gray-600 mb-1 block">
                            Age
                        </label>

                        <input type="number"
                               name="age"
                               value="{{ $volunteer['age'] ?? '25' }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>

                </div>

            </div>

            <!-- SKILLS -->
            <div class="mb-8">

                <h2 class="text-lg font-semibold mb-4 text-gray-700">
                    Volunteer Skills
                </h2>

                <div class="grid grid-cols-3 gap-4">

                    @php
                        $skills = $volunteer['skills'] ?? [];
                    @endphp

                    @foreach(['First Aid','Education','Logistics','Finance','Mentoring','Language'] as $skill)

                    <label class="flex items-center gap-2 bg-gray-50 border rounded-lg p-3">

                        <input type="checkbox"
                               name="skills[]"
                               value="{{ $skill }}"
                               {{ in_array($skill, $skills) ? 'checked' : '' }}>

                        {{ $skill }}

                    </label>

                    @endforeach

                </div>

            </div>

            <!-- STATUS -->
            <div class="mb-8">

                <h2 class="text-lg font-semibold mb-4 text-gray-700">
                    Volunteer Status
                </h2>

                <select name="status"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400">

                    <option value="Active"
                        {{ ($volunteer['status'] ?? '') == 'Active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="Pending"
                        {{ ($volunteer['status'] ?? '') == 'Pending' ? 'selected' : '' }}>
                        Pending
                    </option>

                    <option value="Inactive"
                        {{ ($volunteer['status'] ?? '') == 'Inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

            <!-- NOTES -->
            <div class="mb-8">

                <h2 class="text-lg font-semibold mb-4 text-gray-700">
                    Additional Notes
                </h2>

                <textarea name="notes"
                          rows="4"
                          class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400">{{ $volunteer['notes'] ?? '' }}</textarea>

            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex justify-end gap-3">

                <a href="/volunteers"
                   class="px-5 py-3 rounded-lg border border-gray-300 hover:bg-gray-100">
                    Cancel
                </a>

                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg shadow">
                    Update Volunteer
                </button>

            </div>

        </form>

    </div>

</div>

@endsection