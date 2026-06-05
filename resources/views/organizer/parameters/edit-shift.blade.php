@extends('layouts.organizer')

@section('content')

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">

        <h1 class="text-2xl font-bold mb-6">
            Edit Shift
        </h1>

        <form action="/parameters/shifts/update/{{ $shift->id }}" method="POST">

            @csrf

            <div class="mb-4">

                <label class="block font-medium mb-2">
                    Shift Name
                </label>

                <input type="text" name="name" value="{{ $shift->name }}" maxlength="50" class="w-full border p-2 rounded"
                    required>

            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">

                <div>

                    <label class="block font-medium mb-2">
                        Start Time
                    </label>

                    <input type="time" name="start_time" value="{{ $shift->start_time }}" class="w-full border p-2 rounded"
                        required>

                </div>

                <div>

                    <label class="block font-medium mb-2">
                        End Time
                    </label>

                    <input type="time" name="end_time" value="{{ $shift->end_time }}" class="w-full border p-2 rounded"
                        required>

                </div>

            </div>

            <div class="mb-4">

                <label class="block font-medium mb-2">
                    Status
                </label>

                <select name="status" class="w-full border p-2 rounded">

                    <option value="Active" {{ $shift->status == 'Active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="Inactive" {{ $shift->status == 'Inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

            <div class="flex gap-2">

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">

                    Update Shift

                </button>

                <a href="/parameters#shifts" class="border px-4 py-2 rounded">

                    Cancel

                </a>

            </div>

        </form>

    </div>

@endsection