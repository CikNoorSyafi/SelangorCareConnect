@extends('layouts.organizer')

@section('content')

    <h1 class="text-2xl font-bold mb-2">Campaign Management</h1>
    @if(session('success'))

        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">

            {{ session('success') }}

        </div>

    @endif

    <p class="text-gray-500 mb-6">
        Create new volunteer initiatives, manage existing events, and track your impact.
    </p>

    <!-- FORM Create New Campaign-->

    <form method="POST" action="/campaign/store">

        @csrf

        <div class="bg-white p-6 rounded shadow">

            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-red-500">Create New Campaign</h2>
                <button class="text-sm text-gray-500 underline">Clear Form</button>
            </div>

            <!-- ROW 1 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <!-- CAMPAIGN NAME -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Campaign Name
                    </label>

                    <input name="campaign_name" required type="text" placeholder="Enter campaign name"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

                <!-- CAMPAIGN TYPE -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Campaign Type
                    </label>

                    <select name="campaign_type" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                        <option value="" selected disabled>
                            Select Campaign Type
                        </option>

                        @foreach($campaignTypes as $type)

                            <option value="{{ $type->name }}">
                                {{ $type->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            <!-- ROW 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <!-- LOCATION -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Selangor District
                    </label>

                    <select id="campaign-location" name="location" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">
                        <option value="">Select district</option>
                        @foreach($districts as $district)
                            <option value="{{ $district }}" {{ old('location') === $district ? 'selected' : '' }}>
                                {{ $district }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Choose the administrative district where the programme takes place. Selayang is in Gombak. Shah Alam areas may fall under Petaling or Klang, so select the correct district.</p>
                    @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                </div>

                <!-- FUNDING TARGET -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Funding Target (RM)
                    </label>

                    <input name="funding_target" required type="number" id="fundingTarget" min="0" step="0.01"
                        value="{{ old('funding_target') }}" placeholder="0.00"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

            </div>


            <!-- ROW 3 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                <!-- START DATE -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Start Date
                    </label>

                    <input name="start_date" type="date" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

                <!-- END DATE -->
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        End Date
                    </label>

                    <input name="end_date" type="date" class=" w-full border border-gray-200 rounded-lg px-4 py-3 text-sm">

                </div>

            </div>


            <!-- ROLES -->
            <div class="mt-4">

                <label class="block text-sm font-semibold mb-2">
                    Required Skills
                </label>

                <select name="skills[]" multiple class="w-full border rounded p-2">

                    @foreach($skills as $skill)

                        <option value="{{ $skill->id }}">
                            {{ $skill->name }}
                        </option>

                    @endforeach

                </select>

                <p class="text-xs text-gray-500 mt-1">
                    Hold CTRL to select multiple skills.
                </p>

            </div>

            <!-- ASSIGN VOLUNTEERS -->
            <div class="mt-6 border p-4 rounded">

                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold">Assign Volunteers</h3>
                    <span class="text-xs text-gray-400">Optional</span>
                </div>

                <div class="flex gap-2 mb-3">
                    <input type="text" placeholder="Search volunteers..." class="border p-2 rounded w-full text-sm">

                    <button class="border px-3 rounded text-sm">Search</button>
                </div>

                <table class="w-full text-sm border">

                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Volunteer</th>
                            <th class="p-2 text-left">Role</th>
                            <th class="p-2 text-left">Shift</th>

                        </tr>
                    </thead>

                    <tbody>

                        @foreach($volunteers as $v)

                            <tr>

                                <td class="p-2">

                                    <label class="flex items-start gap-2">

                                        <input type="checkbox" name="volunteers[]" value="{{ $v->id }}" class="mt-1">

                                        <div>

                                            {{ $v->name }}



                                        </div>

                                    </label>

                                </td>

                                <td class="p-2">

                                    <select name="role_id[{{ $v->id }}]" class="border p-1 rounded">

                                        @foreach($roles as $role)

                                            <option value="{{ $role->id }}">
                                                {{ $role->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </td>

                                <td class="p-2">

                                    <select name="shift_id[{{ $v->id }}]" class="border p-1 rounded">

                                        @foreach($shifts as $shift)

                                            <option value="{{ $shift->id }}">
                                                {{ $shift->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>


            </div>

            <textarea name="description" class="border p-3 rounded w-full mt-4" placeholder="Description"></textarea>

            <div class="flex justify-end mt-4 gap-2">
                <button class="px-4 py-2 border rounded">Cancel</button>
                <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded">
                    Save Campaign
                </button>
            </div>
        </div>

    </form>




    <script>

        document.querySelector('form')
            .addEventListener(
                'keydown',
                function (e) {

                    if (e.key === 'Enter') {

                        e.preventDefault();

                    }

                }
            );

    </script>

@endsection