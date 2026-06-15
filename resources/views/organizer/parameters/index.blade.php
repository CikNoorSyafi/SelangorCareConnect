@extends('layouts.organizer')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    <div class="p-6">

        <div class="mb-6">

            <h1 class="text-3xl font-bold">
                System Parameters
            </h1>
            @if(session('success'))

                <div
                    class="mb-4 p-4 rounded-lg
                                                                                                                                                                                                                                                                                                                                                                                                                    bg-green-100
                                                                                                                                                                                                                                                                                                                                                                                                                    text-green-700
                                                                                                                                                                                                                                                                                                                                                                                                                    border border-green-300">

                    {{ session('success') }}

                </div>

            @endif

            <p class="text-gray-600 mt-2">
                Configure and manage skills, roles and shifts.
            </p>

        </div>

        <div class="bg-white rounded-lg shadow">

            {{-- Tabs --}}
            <div class="border-b">

                <ul class="flex">

                    <li>
                        <button onclick="showTab('skills')" id="skills-tab"
                            class="tab-btn px-6 py-4 font-semibold border-b-2 border-red-500 text-red-500">
                            Skills
                        </button>
                    </li>

                    <li>
                        <button onclick="showTab('roles')" id="roles-tab" class="tab-btn px-6 py-4 font-semibold">
                            Roles
                        </button>
                    </li>

                    <li>
                        <button onclick="showTab('shifts')" id="shifts-tab" class="tab-btn px-6 py-4 font-semibold">
                            Shifts
                        </button>
                    </li>
                    <li>
                        <button onclick="showTab('campaign-types')" id="campaign-types-tab"
                            class="tab-btn px-6 py-4 font-semibold">

                            Campaign Types

                        </button>
                    </li>

                </ul>

            </div>

            {{-- Skills Tab --}}
            <div id="skills" class="tab-content p-6">

                <div class="flex justify-between mb-4">

                    <h2 class="text-xl font-semibold">
                        Skills Inventory
                    </h2>

                    <button onclick="document.getElementById('addSkillModal').classList.remove('hidden')"
                        class="bg-red-500 text-white px-4 py-2 rounded">

                        Add Skill

                    </button>

                </div>

                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left py-3">
                                Skill Name
                            </th>

                            <th class="text-left py-3">
                                Description
                            </th>

                            <th class="text-left py-3">
                                Status
                            </th>

                            <th class="text-left py-3">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($skills as $skill)

                            <tr class="border-b">

                                <td class="py-3">
                                    {{ $skill->name }}
                                </td>

                                <td class="max-w-md truncate" title="{{ $skill->description }}">

                                    {{ Str::limit($skill->description, 80) }}

                                </td>

                                <td>

                                    @if($skill->status == 'Active')

                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">
                                            Active
                                        </span>

                                    @else

                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-sm">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td class="py-3">

                                    <div class="flex items-center gap-2">

                                        <a href="/parameters/skills/edit/{{ $skill->id }}"
                                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
                                            Edit
                                        </a>
                                        <form action="/parameters/skills/delete/{{ $skill->id }}" method="POST" class="inline"
                                            onsubmit="return confirm('Skill: {{ $skill->name }} will be permanently deleted. Are you sure?')">

                                            @csrf

                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">

                                                Delete

                                            </button>

                                        </form>
                                    </div>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{-- Roles Tab --}}
            <div id="roles" class="tab-content p-6 hidden">

                <div class="flex justify-between mb-4">

                    <h2 class="text-xl font-semibold">
                        Volunteer Roles
                    </h2>

                    <button onclick="document.getElementById('addRoleModal').classList.remove('hidden')"
                        class="bg-red-500 text-white px-4 py-2 rounded">

                        Add Role

                    </button>

                </div>

                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left py-3">
                                Role Name
                            </th>

                            <th class="text-left py-3">
                                Description
                            </th>

                            <th class="text-left py-3">
                                Status
                            </th>

                            <th class="text-left py-3">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($roles as $role)

                            <tr class="border-b">

                                <td class="py-3">
                                    {{ $role->name }}
                                </td>

                                <td>
                                    {{ $role->description }}
                                </td>

                                <td>
                                    {{ $role->status }}
                                </td>

                                <td class="py-3">

                                    <div class="flex items-center gap-2">

                                        <a href="/parameters/roles/edit/{{ $role->id }}"
                                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm">

                                            Edit

                                        </a>

                                        <form action="/parameters/roles/delete/{{ $role->id }}" method="POST" class="inline"
                                            onsubmit="return confirm('Role: {{ $role->name }} will be permanently deleted. Are you sure?')">

                                            @csrf

                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">

                                                Delete

                                            </button>

                                        </form>
                                    </div>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{-- Shifts Tab --}}
            <div id="shifts" class="tab-content p-6 hidden">

                <div class="flex justify-between mb-4">

                    <h2 class="text-xl font-semibold">
                        Shift Schedule
                    </h2>

                    <button onclick="document.getElementById('addShiftModal').classList.remove('hidden')"
                        class="bg-red-500 text-white px-4 py-2 rounded">

                        Add Shift

                    </button>

                </div>

                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left py-3">
                                Shift Name
                            </th>

                            <th class="text-left py-3">
                                Start Time
                            </th>

                            <th class="text-left py-3">
                                End Time
                            </th>

                            <th class="text-left py-3">
                                Status
                            </th>

                            <th class="text-left py-3">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($shifts as $shift)

                            <tr class="border-b">

                                <td class="py-3">
                                    {{ $shift->name }}
                                </td>

                                <td>
                                    {{ $shift->start_time }}
                                </td>

                                <td>
                                    {{ $shift->end_time }}
                                </td>

                                <td>
                                    {{ $shift->status }}
                                </td>

                                <td class="py-3">

                                    <div class="flex items-center gap-2">

                                        <a href="/parameters/shifts/edit/{{ $shift->id }}"
                                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm">

                                            Edit

                                        </a>

                                        <form action="/parameters/shifts/delete/{{ $shift->id }}" method="POST" class="inline"
                                            onsubmit="return confirm('Shift: {{ $shift->name }} will be permanently deleted. Are you sure?')">

                                            @csrf

                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">

                                                Delete

                                            </button>

                                        </form>
                                    </div>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{-- Campaign Types --}}
            <div id="campaign-types" class="tab-content p-6 hidden">

                <div class="flex justify-between mb-4">

                    <h2 class="text-xl font-semibold">
                        Campaign Types
                    </h2>

                    <button onclick="document.getElementById('addCampaignTypeModal').classList.remove('hidden')"
                        class="bg-red-500 text-white px-4 py-2 rounded">

                        Add Campaign Type

                    </button>

                </div>

                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left py-3">
                                Campaign Type Name
                            </th>

                            <th class="text-left py-3">
                                Description
                            </th>

                            <th class="text-left py-3">
                                Status
                            </th>

                            <th class="text-left py-3">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($campaignTypes as $campaignType)

                            <tr class="border-b">

                                <td class="py-3">
                                    {{ $campaignType->name }}
                                </td>

                                <td>
                                    {{ Str::limit($campaignType->description, 80) }}
                                </td>

                                <td>
                                    {{ $campaignType->status }}
                                </td>



                                <td class="py-3">

                                    <div class="flex items-center gap-2">

                                        <a href="/parameters/campaign-types/edit/{{ $campaignType->id }}"
                                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm">

                                            Edit

                                        </a>

                                        <form action="/parameters/campaign-types/delete/{{ $campaignType->id }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Campaign Type: {{ $campaignType->name }} will be permanently deleted. Are you sure?')">

                                            @csrf

                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">

                                                Delete

                                            </button>

                                        </form>
                                    </div>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script>

        function showTab(tabName) {

            document
                .querySelectorAll('.tab-content')
                .forEach(tab => tab.classList.add('hidden'));

            document
                .querySelectorAll('.tab-btn')
                .forEach(btn => {

                    btn.classList.remove(
                        'border-b-2',
                        'border-red-500',
                        'text-red-500'
                    );

                });

            document
                .getElementById(tabName)
                .classList.remove('hidden');

            document
                .getElementById(tabName + '-tab')
                .classList.add(
                    'border-b-2',
                    'border-red-500',
                    'text-red-500'
                );
        }



    </script>
    <div id="addSkillModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white rounded-lg p-6 w-full max-w-lg">

            <h2 class="text-xl font-bold mb-4">
                Add Skill
            </h2>

            <form action="/parameters/skills/store" method="POST">

                @csrf

                <input type="text" name="name" placeholder="Skill Name" maxlength="50"
                    class="w-full border p-2 rounded mb-3" required>

                <textarea name="description" maxlength="255" rows="3" class="w-full border p-2 rounded mb-3"
                    placeholder="Description"></textarea>

                <select name="status" class="w-full border p-2 rounded mb-4">

                    <option>Active</option>
                    <option>Inactive</option>

                </select>

                <div class="flex justify-end gap-2">

                    <button type="button" onclick="document.getElementById('addSkillModal').classList.add('hidden')"
                        class="px-4 py-2 border rounded">

                        Cancel

                    </button>

                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">

                        Save

                    </button>

                </div>

            </form>

        </div>

    </div>
    <div id="editSkillModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white rounded-lg p-6 w-full max-w-lg">

            <h2 class="text-xl font-bold mb-4">
                Edit Skill
            </h2>

            <form id="editSkillForm" method="POST" action="/parameters">

                @csrf

                <input type="hidden" id="editSkillId">

                <input type="text" id="editSkillName" name="name" class="w-full border p-2 rounded mb-3" required>

                <textarea id="editSkillDescription" name="description" class="w-full border p-2 rounded mb-3"></textarea>

                <select id="editSkillStatus" name="status" class="w-full border p-2 rounded mb-4">

                    <option value="Active">
                        Active
                    </option>

                    <option value="Inactive">
                        Inactive
                    </option>

                </select>

                <div class="flex justify-end gap-2">

                    <button type="button" onclick="document.getElementById('editSkillModal').classList.add('hidden')"
                        class="border px-4 py-2 rounded">

                        Cancel

                    </button>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">

                        Update

                    </button>

                </div>

            </form>

        </div>

    </div>

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white rounded-lg p-6 w-full max-w-lg">

            <h2 class="text-xl font-bold mb-4">
                Add Role
            </h2>

            <form action="/parameters/roles/store" method="POST">

                @csrf

                <div class="mb-3">

                    <label class="block text-sm font-medium mb-1">
                        Role Name
                    </label>

                    <input type="text" name="name" maxlength="50" class="w-full border p-2 rounded" required>

                </div>

                <div class="mb-3">

                    <label class="block text-sm font-medium mb-1">
                        Description
                    </label>

                    <textarea name="description" rows="3" maxlength="255" class="w-full border p-2 rounded"></textarea>

                </div>

                <div class="mb-4">

                    <label class="block text-sm font-medium mb-1">
                        Status
                    </label>

                    <select name="status" class="w-full border p-2 rounded">

                        <option value="Active">
                            Active
                        </option>

                        <option value="Inactive">
                            Inactive
                        </option>

                    </select>

                </div>

                <div class="flex justify-end gap-2">

                    <button type="button" onclick="document.getElementById('addRoleModal').classList.add('hidden')"
                        class="px-4 py-2 border rounded">

                        Cancel

                    </button>

                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">

                        Save

                    </button>

                </div>

            </form>

        </div>

    </div>

    <!-- Add Shift Modal -->
    <div id="addShiftModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white rounded-lg p-6 w-full max-w-lg">

            <h2 class="text-xl font-bold mb-4">
                Add Shift
            </h2>

            <form action="/parameters/shifts/store" method="POST">

                @csrf

                <input type="text" name="name" maxlength="50" placeholder="Shift Name"
                    class="w-full border p-2 rounded mb-3" required>

                <label class="block mb-1">
                    Start Time
                </label>

                <input type="time" name="start_time" class="w-full border p-2 rounded mb-3" required>

                <label class="block mb-1">
                    End Time
                </label>

                <input type="time" name="end_time" class="w-full border p-2 rounded mb-3" required>

                <select name="status" class="w-full border p-2 rounded mb-4">

                    <option value="Active">
                        Active
                    </option>

                    <option value="Inactive">
                        Inactive
                    </option>

                </select>

                <div class="flex justify-end gap-2">

                    <button type="button" onclick="document.getElementById('addShiftModal').classList.add('hidden')"
                        class="border px-4 py-2 rounded">

                        Cancel

                    </button>

                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">

                        Save

                    </button>

                </div>

            </form>

        </div>

    </div>
    <!-- Add Campaign Type Modal -->
    <div id="addCampaignTypeModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white rounded-lg p-6 w-full max-w-lg">

            <h2 class="text-xl font-bold mb-4">
                Add Campaign Type
            </h2>

            <form action="/parameters/campaign-types/store" method="POST">

                @csrf

                <input type="text" name="name" maxlength="50" placeholder="Campaign Type Name"
                    class="w-full border p-2 rounded mb-3" required>

                <textarea name="description" maxlength="255" rows="3" class="w-full border p-2 rounded mb-3"
                    placeholder="Description"></textarea>

                <select name="status" class="w-full border p-2 rounded mb-4">

                    <option value="Active">
                        Active
                    </option>

                    <option value="Inactive">
                        Inactive
                    </option>

                </select>

                <div class="flex justify-end gap-2">

                    <button type="button" onclick="document.getElementById('addCampaignTypeModal').classList.add('hidden')"
                        class="border px-4 py-2 rounded">

                        Cancel

                    </button>

                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">

                        Save

                    </button>

                </div>

            </form>

        </div>

    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            if (window.location.hash === '#roles') {

                document.getElementById('roles-tab').click();

            }

            if (window.location.hash === '#shifts') {

                document.getElementById('shifts-tab').click();

            }
            if (window.location.hash === '#campaign-types') {

                document
                    .getElementById('campaign-types-tab')
                    .click();

            }

        });

    </script>
@endsection