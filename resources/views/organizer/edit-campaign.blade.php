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
                Location
            </label>

            <input type="text" name="location" value="{{ $campaign->location }}" list="selangor-locations"
                class="border w-full p-2 rounded mb-4">

            <datalist id="selangor-locations">

                @foreach($locations as $location)

                    <option value="{{ $location }}">

                @endforeach

            </datalist>

            <label class="block mb-2">
                Funding Target
            </label>

            <input type="text" id="fundingTarget" name="funding_target"
                value="{{ number_format((float) $campaign['target'], 2) }}" class="border w-full p-2 rounded mb-4">

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

                                            <span class="text-gray-500 text-xs">
                                                (ID: {{ $v->id }})
                                            </span>

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

        const fundingInput = document.getElementById('fundingTarget');

        fundingInput.addEventListener('input', function (e) {

            let value = e.target.value;

            value = value.replace(/[^0-9.]/g, '');

            const parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts[1];
            }

            if (parts[1]) {
                parts[1] = parts[1].substring(0, 2);
                value = parts[0] + '.' + parts[1];
            }

            e.target.value = value;

        });

        fundingInput.addEventListener('blur', function (e) {

            let value = parseFloat(e.target.value);

            if (!isNaN(value)) {
                e.target.value = value.toLocaleString('en-MY', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

        });

    </script>
@endsection