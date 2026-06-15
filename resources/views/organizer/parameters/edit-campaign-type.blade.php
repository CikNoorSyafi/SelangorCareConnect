@extends('layouts.organizer')

@section('content')

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">

        <h1 class="text-2xl font-bold mb-6">
            Edit Campaign Type
        </h1>

        <form action="/parameters/campaign-types/update/{{ $campaignType->id }}" method="POST">

            @csrf

            <div class="mb-4">

                <label class="block font-medium mb-2">
                    Campaign Type Name
                </label>

                <input type="text" name="name" value="{{ $campaignType->name }}" maxlength="50"
                    class="w-full border p-2 rounded" required>

            </div>

            <div class="mb-4">

                <label class="block font-medium mb-2">
                    Description
                </label>

                <textarea name="description" rows="4" maxlength="255"
                    class="w-full border p-2 rounded">{{ $campaignType->description }}</textarea>

            </div>

            <div class="mb-4">

                <label class="block font-medium mb-2">
                    Status
                </label>

                <select name="status" class="w-full border p-2 rounded">

                    <option value="Active" {{ $campaignType->status == 'Active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="Inactive" {{ $campaignType->status == 'Inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

            <div class="flex gap-2">

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">

                    Update Campaign Type

                </button>

                <a href="/parameters/campaign-types" class="border px-4 py-2 rounded">

                    Cancel

                </a>

            </div>

        </form>

    </div>

@endsection