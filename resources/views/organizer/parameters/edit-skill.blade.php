@extends('layouts.organizer')

@section('content')

    <div class="max-w-4xl">

        <h1 class="text-3xl font-bold mb-6">
            Edit Skill
        </h1>

        <div class="bg-white rounded-lg shadow p-6">

            <form action="/parameters/skills/update/{{ $skill->id }}" method="POST">

                @csrf

                <!-- Skill Name -->
                <div class="mb-5">

                    <label class="block font-medium mb-2">
                        Skill Name
                    </label>

                    <input type="text" name="name" value="{{ $skill->name }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400"
                        required>

                </div>

                <!-- Description -->
                <div class="mb-5">

                    <label class="block font-medium mb-2">
                        Description
                    </label>

                    <textarea name="description" rows="4"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400">{{ $skill->description }}</textarea>

                </div>

                <!-- Status -->
                <div class="mb-6">

                    <label class="block font-medium mb-2">
                        Status
                    </label>

                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-400">

                        <option value="Active" {{ $skill->status == 'Active' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="Inactive" {{ $skill->status == 'Inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>

                </div>

                <!-- Buttons -->
                <div class="flex gap-3">

                    <a href="/parameters" class="px-5 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                        Cancel
                    </a>

                    <button type="submit" class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600">

                        Update Skill

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection