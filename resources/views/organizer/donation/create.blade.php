@extends('layouts.organizer')

@section('content')

    <div class="max-w-4xl mx-auto bg-white border rounded-2xl p-8">

        <!-- HEADER -->
        <div class="mb-8">

            <h1 class="text-4xl font-black text-gray-900">
                Record Manual Donation
            </h1>

            <p class="text-gray-500 mt-2">
                Add offline or manual donation records into the system.
            </p>

        </div>

        <!-- FORM -->
        <form action="/donation/store" method="POST">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- CONTRIBUTOR -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Contributor Name
                    </label>

                    <input type="text" name="contributor" required
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                </div>

                <!-- CAMPAIGN -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Campaign
                    </label>

                    <input type="text" name="campaign" required
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                </div>
                <!-- CAMPAIGN TYPE -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Campaign Type
                    </label>

                    <input type="text" name="campaign_type" required
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                </div>

                <!-- AMOUNT -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Amount (RM)
                    </label>

                    <input type="text" id="amount" name="amount" maxlength="13" required placeholder="0.00"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">
                </div>

                <!-- STATUS -->
                <div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Status
                    </label>

                    <select name="status"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:outline-none">

                        <option value="Allocated">
                            Allocated
                        </option>

                        <option value="Pending">
                            Pending
                        </option>

                    </select>

                </div>

            </div>

            <!-- BUTTON -->
            <div class="flex gap-4 mt-8">

                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">

                    Save Donation

                </button>

                <a href="/donation" class="px-6 py-3 rounded-xl border hover:bg-gray-50 transition">

                    Cancel

                </a>

            </div>

        </form>

    </div>

    <script>

        const amountInput = document.getElementById('amount');

        // Allow only numbers and decimal
        amountInput.addEventListener('input', function (e) {

            let value = e.target.value;

            // Remove invalid chars
            value = value.replace(/[^0-9.]/g, '');

            // Prevent multiple decimals
            const parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts[1];
            }

            // Limit integer part to 10 digits
            if (parts[0].length > 10) {
                parts[0] = parts[0].substring(0, 10);
                value = parts.join('.');
            }

            // Limit decimal places to 2
            if (parts[1]) {
                parts[1] = parts[1].substring(0, 2);
                value = parts[0] + '.' + parts[1];
            }

            e.target.value = value;
        });

        // Format nicely when leaving field
        amountInput.addEventListener('blur', function (e) {

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