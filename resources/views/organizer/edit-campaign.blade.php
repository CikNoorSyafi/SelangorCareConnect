@extends('layouts.organizer')

@section('content')

    <h1 class="text-2xl font-bold mb-6">
        Edit Campaign
    </h1>

    <form method="POST" action="/campaign/update/{{ $campaign->id }}">

        @csrf

        <div class="bg-white p-6 rounded shadow">

            <label class="block mb-2">
                Campaign Name
            </label>

            <input type="text" name="campaign_name" value="{{ $campaign->name }}" class="border w-full p-2 rounded mb-4">

            <label class="block mb-2">
                Campaign Type
            </label>

            <select name="campaign_type" class="border w-full p-2 rounded mb-4">

                @foreach($campaignTypes as $type)

                    <option value="{{ $type->name }}" {{ $campaign->type == $type->name ? 'selected' : '' }}>

                        {{ $type->name }}

                    </option>

                @endforeach

            </select>

            <label class="block mb-2">
                Selangor District
            </label>

            <select id="campaign-location" name="location" required class="border w-full p-2 rounded mb-4">
                <option value="">Select district</option>
                @foreach($districts as $district)
                    <option value="{{ $district }}" {{ old('location', $selectedDistrict) === $district ? 'selected' : '' }}>
                        {{ $district }}
                    </option>
                @endforeach
            </select>
            <p class="mb-3 text-xs text-gray-500">Choose the administrative district where the programme takes place. Selayang is in Gombak. Shah Alam areas may fall under Petaling or Klang, so select the correct district.</p>
            @error('location') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror

            <label class="block mb-2">
                Funding Target
            </label>

            <input type="number" id="fundingTarget" name="funding_target" min="0" step="0.01"
                value="{{ old('funding_target', $campaign->target) }}" class="border w-full p-2 rounded mb-4">
            <label class="block mb-2">
                Required Skills
            </label>

            <select name="skills[]" multiple class="border w-full p-2 rounded mb-4" size="6">

                @foreach($skills as $skill)

                    <option value="{{ $skill->id }}" @if(
                        in_array(
                            $skill->id,
                            $selectedSkillIds
                        )
                    ) selected @endif>

                        {{ $skill->name }}

                    </option>

                @endforeach

            </select>

            <p class="text-xs text-gray-500 mb-4">
                Hold CTRL to select multiple skills.
            </p>
            <label class="block mb-2">
                Description
            </label>

            <textarea name="description" class="border w-full p-2 rounded mb-4">{{ $campaign->description }}</textarea>

            <h3 class="font-semibold text-lg mt-6 mb-3">
                Assign Volunteers
            </h3>

            <div class="border rounded p-4 mb-4 overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left p-2">
                                Volunteer
                            </th>

                            <th class="text-left p-2">
                                Role
                            </th>

                            <th class="text-left p-2">
                                Shift
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($volunteers as $v)

                            <tr class="border-b">

                                <td class="p-2">

                                    <label class="flex items-center gap-2">

                                        <input type="checkbox" name="volunteers[]" value="{{ $v->id }}" @if(
                                                isset($assignedVolunteerIds)
                                                && in_array(
                                                    $v->id,
                                                    $assignedVolunteerIds
                                                )
                                            ) checked
                                        @endif>

                                        <span>

                                            {{ $v->name }}


                                        </span>

                                    </label>

                                </td>

                                <td class="p-2">

                                    <select name="role_id[{{ $v->id }}]" class="border p-2 rounded w-full">

                                        @foreach($roles as $role)

                                            <option value="{{ $role->id }}" @if(
                                                isset($assignedAssignments[$v->id])
                                                &&
                                                $assignedAssignments[$v->id]->role_id == $role->id
                                            ) selected @endif>

                                                {{ $role->name }}

                                            </option>

                                        @endforeach

                                    </select>

                                </td>

                                <td class="p-2">

                                    <select name="shift_id[{{ $v->id }}]" class="border p-2 rounded w-full">

                                        @foreach($shifts as $shift)

                                            <option value="{{ $shift->id }}" @if(
                                                isset($assignedAssignments[$v->id])
                                                &&
                                                $assignedAssignments[$v->id]->shift_id == $shift->id
                                            ) selected @endif>

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
            <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded">

                Update Campaign

            </button>

        </div>

    </form>


    <script>

    </script>
@endsection