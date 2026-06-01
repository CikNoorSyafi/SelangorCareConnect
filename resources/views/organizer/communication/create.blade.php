@extends('layouts.organizer')

@section('content')

    <div class="max-w-5xl mx-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-10">

            <div>

                <h1 class="text-5xl font-black text-gray-900">

                    Create Notification

                </h1>

                <p class="text-gray-500 text-xl mt-3">

                    Send manual or system notifications.

                </p>

            </div>

            <!-- BACK BUTTON -->
            <a href="/communication" class="px-6 py-4 border rounded-2xl hover:bg-gray-50 transition">

                Back

            </a>

        </div>

        <!-- FORM -->
        <form action="/communication/store" method="POST" class="bg-white border rounded-3xl p-8">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- TITLE -->
                <div>

                    <label class="block mb-2 font-semibold">

                        Notification Title

                    </label>

                    <input type="text" name="title" required class="w-full border rounded-2xl px-5 py-4">

                </div>

                <!-- TYPE -->
                <div>

                    <label class="block mb-2 font-semibold">

                        Notification Type

                    </label>

                    <select id="notificationType" name="type" class="w-full border rounded-2xl px-5 py-4">

                        <option>Manual</option>
                        <option>System</option>

                    </select>

                </div>

                <!-- SYSTEM TEMPLATE -->
                <div id="systemTemplateBox" class="hidden">

                    <label class="block mb-2 font-semibold">

                        System Template

                    </label>

                    <select name="system_template" class="w-full border rounded-2xl px-5 py-4">

                        <option>Event Reminder</option>

                        <option>Donation Allocation</option>

                        <option>Campaign Completion</option>

                        <option>Volunteer Assignment</option>

                    </select>

                </div>

                <!-- AUDIENCE -->
                <div>

                    <label class="block mb-2 font-semibold">

                        Audience

                    </label>

                    <select name="audience" class="w-full border rounded-2xl px-5 py-4">

                        <option>All Volunteers</option>
                        <option>Campaign Volunteers</option>
                        <option>All Donors</option>
                        <option>Campaign Donors</option>

                    </select>

                </div>

                <!-- CAMPAIGN -->
                <div>

                    <label class="block mb-2 font-semibold">

                        Campaign

                    </label>

                    <input type="text" name="campaign" class="w-full border rounded-2xl px-5 py-4">

                </div>

            </div>
            <div>

                <label class="block mb-2 font-semibold">

                    Notification Status

                </label>

                <select name="status" class="w-full border rounded-2xl px-5 py-4">

                    <option>Draft</option>

                    <option>Sent</option>

                    <option>Delivered</option>

                    <option>Failed</option>

                </select>

            </div>

            <!-- MESSAGE -->
            <div id="messageBox" class="mt-6">

                <label class="block mb-2 font-semibold">

                    Message

                </label>

                <textarea name="message" rows="6" class="w-full border rounded-2xl px-5 py-4"></textarea>

            </div>

            <!-- BUTTONS -->
            <div class="flex gap-4 mt-8">

                <button type="submit"
                    class="bg-red-500 text-white px-8 py-4 rounded-2xl font-semibold hover:bg-red-600 transition">

                    Send Notification

                </button>

                <a href="/communication" class="px-8 py-4 border rounded-2xl hover:bg-gray-50 transition">

                    Cancel

                </a>

            </div>

        </form>

    </div>

    <script>

        const notificationType =
            document.getElementById('notificationType');

        const systemTemplateBox =
            document.getElementById('systemTemplateBox');

        const messageBox =
            document.getElementById('messageBox');

        notificationType.addEventListener('change', function () {

            if (this.value === 'System') {

                systemTemplateBox.classList.remove('hidden');

                messageBox.classList.add('hidden');

            } else {

                systemTemplateBox.classList.add('hidden');

                messageBox.classList.remove('hidden');
            }

        });

    </script>
@endsection