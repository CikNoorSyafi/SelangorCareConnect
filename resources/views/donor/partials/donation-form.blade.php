<div class="mx-auto max-w-7xl">
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            <p class="font-semibold">Please check the following:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-500">SelangorCareConnect+ · Contribution</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Make Donation</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
            {{ $donationType === 'general'
                ? 'Contribute to the shared fund supporting community needs and future initiatives.'
                : 'Choose a campaign, review its progress and make a contribution to support the community.' }}
        </p>
    </div>

    <form id="donationForm" method="POST" action="{{ route('donor.payment.start') }}">
        @csrf
        <input type="hidden" name="donation_type" value="{{ $donationType }}">
        @if($donationType === 'general')
            <input type="hidden" name="campaign_id" value="">
        @endif

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[0.92fr_1.08fr]">
            {{-- Left column: campaign selector and live campaign summary. --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @if($donationType === 'campaign')
                    <div class="border-b border-slate-100 px-6 py-5">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Target campaign</p>
                                <h2 class="mt-1 text-lg font-bold text-slate-900">Choose where to contribute</h2>
                            </div>
                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">Campaign Fund</span>
                        </div>

                        @if($campaigns->isEmpty())
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                There are currently no campaigns accepting donations. Please check again later.
                            </div>
                        @else
                            <label for="campaign_id" class="mb-2 block text-sm font-semibold text-slate-700">Available campaigns</label>
                            <select id="campaign_id" name="campaign_id" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100">
                                <option value="">Choose a campaign</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" @selected((string) old('campaign_id', $selectedCampaignId) === (string) $campaign->id)>
                                        {{ $campaign->name }} — {{ $campaign->location ?: 'Location to be confirmed' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-slate-400">Showing all campaigns currently open for donations.</p>
                        @endif
                    </div>

                    <div class="p-5 sm:p-6">
                        <div id="campaignSummary" class="{{ $selectedCampaign ? '' : 'hidden' }}">
                            <div class="relative mb-5 min-h-[170px] overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-slate-800 to-red-700 p-6 text-white">
                                <div class="absolute -right-8 -top-12 h-48 w-48 rounded-full border-[24px] border-white/10"></div>
                                <div class="absolute -bottom-16 right-20 h-40 w-40 rounded-full bg-red-400/20 blur-2xl"></div>
                                <div class="relative flex min-h-[122px] flex-col justify-between">
                                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-white/70">
                                        <span class="material-symbols-outlined text-lg">volunteer_activism</span>
                                        Community programme
                                    </div>
                                    <div>
                                        <p id="summaryType" class="mb-1 text-xs font-semibold text-red-200">{{ $selectedCampaign?->type ?: 'Community Campaign' }}</p>
                                        <h3 id="summaryName" class="max-w-lg text-2xl font-black leading-tight">{{ $selectedCampaign?->name ?? 'Select a campaign' }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500">
                                <span class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-red-500">location_on</span><span id="summaryLocation">{{ $selectedCampaign?->location ?: 'Select a campaign to view its area' }}</span></span>
                                <span id="summaryDates" class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-slate-400">calendar_month</span><span>{{ $selectedCampaign?->start_date ? \Carbon\Carbon::parse($selectedCampaign->start_date)->format('d M Y') : 'To be announced' }} – {{ $selectedCampaign?->end_date ? \Carbon\Carbon::parse($selectedCampaign->end_date)->format('d M Y') : 'To be announced' }}</span></span>
                            </div>

                            <p id="summaryDescription" class="mb-6 text-sm leading-6 text-slate-600">{{ $selectedCampaign?->description ?: 'Support this community programme and help its beneficiaries.' }}</p>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <span class="text-sm font-semibold text-slate-600">Fundraising progress</span>
                                    <span id="summaryProgressText" class="text-sm font-bold text-emerald-700">{{ number_format((float) ($selectedCampaign?->funding_progress ?? 0), 1) }}%</span>
                                </div>
                                <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                                    <div id="summaryProgressBar" class="h-full rounded-full bg-emerald-500 transition-all duration-300" style="width: {{ (float) ($selectedCampaign?->funding_progress ?? 0) }}%"></div>
                                </div>
                                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                                    <span>Raised <strong id="summaryCollected" class="text-slate-800">RM {{ number_format((float) ($selectedCampaign?->collected_amount ?? 0), 2) }}</strong></span>
                                    <span>Target <strong id="summaryTarget" class="text-slate-800">RM {{ number_format((float) ($selectedCampaign?->target ?? 0), 2) }}</strong></span>
                                </div>
                            </div>
                            <p id="summaryStatus" class="mt-4 text-xs font-semibold text-emerald-700">{{ $selectedCampaign ? ($selectedCampaign->status === 'Approved' ? 'Open for donations' : $selectedCampaign->status) : '' }}</p>
                        </div>

                        <div id="campaignSummaryEmpty" class="{{ $selectedCampaign ? 'hidden' : '' }} rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center">
                            <span class="material-symbols-outlined text-4xl text-slate-300">campaign</span>
                            <p class="mt-3 font-semibold text-slate-700">Select a campaign to see its summary</p>
                            <p class="mt-1 text-sm text-slate-500">Campaign location, fundraising progress and programme details will appear here.</p>
                        </div>
                    </div>
                @else
                    <div class="p-5 sm:p-6">
                        <div class="mb-5 rounded-xl bg-gradient-to-br from-red-600 via-red-500 to-orange-400 p-6 text-white">
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-white/80">
                                <span class="material-symbols-outlined text-lg">volunteer_activism</span>
                                Shared community contribution
                            </div>
                            <h2 class="mt-8 text-3xl font-black">General Fund</h2>
                            <p class="mt-2 max-w-md text-sm leading-6 text-white/85">Support community needs and future initiatives across Selangor.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">General Fund contributions received</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">RM {{ number_format((float) $communityFundTotal, 2) }}</p>
                            <p class="mt-2 text-sm text-slate-500">Total successful contributions recorded across the platform.</p>
                        </div>
                    </div>
                @endif
            </section>

            {{-- Right column: contribution details. --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="border-b border-slate-100 pb-5">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-red-500">Your contribution</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-900">Make Donation</h2>
                    <p class="mt-1 text-sm text-slate-500">Choose an amount and provide your receipt details.</p>
                </div>

                <div class="border-b border-slate-100 py-5">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <label class="text-sm font-semibold text-slate-700">Contribution frequency</label>
                        <span class="text-xs text-slate-400">Choose one</span>
                    </div>
                    <div class="grid grid-cols-2 rounded-xl bg-slate-100 p-1">
                        <label class="cursor-pointer">
                            <input class="peer sr-only" type="radio" name="frequency" value="one_time" checked>
                            <span class="flex justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 transition peer-checked:bg-white peer-checked:text-red-600 peer-checked:shadow-sm">One-Time</span>
                        </label>
                        <label class="cursor-pointer">
                            <input class="peer sr-only" type="radio" name="frequency" value="monthly">
                            <span class="flex justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 transition peer-checked:bg-white peer-checked:text-red-600 peer-checked:shadow-sm">Monthly</span>
                        </label>
                    </div>
                    <p id="monthlyNotice" class="mt-2 hidden rounded-lg bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-800">
                        Monthly is recorded as your preferred contribution frequency for this demonstration. Automatic recurring charges are not enabled.
                    </p>
                </div>

                <div class="border-b border-slate-100 py-5">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <label class="text-sm font-semibold text-slate-700" for="customAmount">Select an amount (RM)</label>
                        <span class="text-xs text-slate-400">Minimum RM1.00</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach([50, 100, 250, 500] as $amount)
                            <button type="button" class="amount-button rounded-lg border border-slate-200 px-3 py-3 text-sm font-bold text-slate-700 transition hover:border-red-300 hover:bg-red-50" data-amount="{{ $amount }}">RM {{ number_format($amount) }}</button>
                        @endforeach
                    </div>
                    <label for="customAmount" class="mb-2 mt-4 block text-sm font-medium text-slate-600">Custom amount (RM)</label>
                    <input id="customAmount" type="number" min="1" max="99999999.99" step="0.01" inputmode="decimal" placeholder="Enter your amount" value="{{ old('amount') }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-base text-slate-900 outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100">
                    <input type="hidden" id="donationAmount" name="amount" value="{{ old('amount') }}">
                </div>

                <div class="border-b border-slate-100 py-5">
                    <h3 class="mb-4 text-sm font-semibold text-slate-700">Donor details</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="donorName" class="mb-2 block text-sm font-medium text-slate-600">Donor full name</label>
                            <input id="donorName" name="donor_name" type="text" maxlength="255" required value="{{ old('donor_name', $donor->name) }}"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100">
                        </div>
                        <div>
                            <label for="receiptEmail" class="mb-2 block text-sm font-medium text-slate-600">Receipt email address</label>
                            <input id="receiptEmail" name="receipt_email" type="email" maxlength="255" required value="{{ old('receipt_email', $donor->email) }}"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="dedication" class="mb-2 block text-sm font-medium text-slate-600">Dedication / note <span class="font-normal text-slate-400">(Optional)</span></label>
                        <textarea id="dedication" name="dedication" rows="3" maxlength="2000" placeholder="Add a message for the campaign or community..."
                            class="w-full resize-y rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100">{{ old('dedication') }}</textarea>
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">Checkout total</p>
                            <p id="checkoutTotal" class="mt-1 text-2xl font-black text-slate-900">RM 0.00</p>
                            <p id="checkoutFrequency" class="mt-1 text-xs font-medium text-slate-500">One-Time contribution</p>
                        </div>
                        <span class="material-symbols-outlined text-3xl text-emerald-600">verified_user</span>
                    </div>
                    <button type="submit" @disabled($donationType === 'campaign' && $campaigns->isEmpty())
                        class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-3.5 font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:opacity-50">
                        Proceed to Payment Gateway
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                    <p class="mt-3 text-center text-xs leading-5 text-slate-400">The next step uses a simulated payment gateway for project demonstration. No real payment is processed.</p>
                </div>
            </section>
        </div>
    </form>
</div>

@if($donationType === 'campaign')
    <script type="application/json" id="campaignSummaryData">@json($campaignSummaryData->all())</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('donationForm');
    if (!form) return;

    const customAmount = document.getElementById('customAmount');
    const amountHidden = document.getElementById('donationAmount');
    const checkoutTotal = document.getElementById('checkoutTotal');
    const checkoutFrequency = document.getElementById('checkoutFrequency');
    const amountButtons = Array.from(document.querySelectorAll('.amount-button'));
    const frequencyInputs = Array.from(document.querySelectorAll('input[name="frequency"]'));
    const monthlyNotice = document.getElementById('monthlyNotice');

    const money = (value) => 'RM ' + Number(value || 0).toLocaleString('en-MY', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    function updateAmount(value) {
        const amount = Number(value);
        const valid = Number.isFinite(amount) && amount >= 1 && amount <= 99999999.99;
        amountHidden.value = valid ? amount.toFixed(2) : '';
        checkoutTotal.textContent = money(valid ? amount : 0);
        amountButtons.forEach((button) => {
            const active = valid && Number(button.dataset.amount) === amount;
            button.classList.toggle('border-red-500', active);
            button.classList.toggle('bg-red-50', active);
            button.classList.toggle('text-red-700', active);
        });
    }

    amountButtons.forEach((button) => {
        button.addEventListener('click', function () {
            customAmount.value = button.dataset.amount;
            updateAmount(button.dataset.amount);
        });
    });

    customAmount.addEventListener('input', function () {
        updateAmount(customAmount.value);
    });

    function updateFrequency() {
        const selected = frequencyInputs.find((input) => input.checked)?.value || 'one_time';
        const monthly = selected === 'monthly';
        checkoutFrequency.textContent = monthly ? 'Monthly contribution preference' : 'One-Time contribution';
        monthlyNotice?.classList.toggle('hidden', !monthly);
    }
    frequencyInputs.forEach((input) => input.addEventListener('change', updateFrequency));

    // Campaign data is read from the database and safely applied as text, not HTML.
    const campaignSelect = document.getElementById('campaign_id');
    const summaryDataElement = document.getElementById('campaignSummaryData');
    const summaryData = summaryDataElement ? JSON.parse(summaryDataElement.textContent || '{}') : {};
    const summary = {
        wrapper: document.getElementById('campaignSummary'),
        empty: document.getElementById('campaignSummaryEmpty'),
        type: document.getElementById('summaryType'),
        name: document.getElementById('summaryName'),
        location: document.getElementById('summaryLocation'),
        dates: document.querySelector('#summaryDates span:last-child'),
        description: document.getElementById('summaryDescription'),
        progressText: document.getElementById('summaryProgressText'),
        progressBar: document.getElementById('summaryProgressBar'),
        collected: document.getElementById('summaryCollected'),
        target: document.getElementById('summaryTarget'),
        status: document.getElementById('summaryStatus')
    };

    function updateCampaignSummary() {
        if (!campaignSelect) return;
        const campaign = summaryData[campaignSelect.value];
        if (!campaign) {
            summary.wrapper?.classList.add('hidden');
            summary.empty?.classList.remove('hidden');
            return;
        }

        summary.wrapper?.classList.remove('hidden');
        summary.empty?.classList.add('hidden');
        summary.type.textContent = campaign.type || 'Community Campaign';
        summary.name.textContent = campaign.name || 'Campaign';
        summary.location.textContent = campaign.location || 'Location to be confirmed';
        summary.dates.textContent = (campaign.start_date || 'To be announced') + ' – ' + (campaign.end_date || 'To be announced');
        summary.description.textContent = campaign.description || 'Support this community programme and help its beneficiaries.';
        summary.progressText.textContent = Number(campaign.progress || 0).toFixed(1) + '%';
        summary.progressBar.style.width = Math.max(0, Math.min(100, Number(campaign.progress || 0))) + '%';
        summary.collected.textContent = money(campaign.collected);
        summary.target.textContent = money(campaign.target);
        summary.status.textContent = campaign.status || 'Open for donations';
    }

    campaignSelect?.addEventListener('change', updateCampaignSummary);

    if (customAmount.value) updateAmount(customAmount.value);
    updateFrequency();
    updateCampaignSummary();

    form.addEventListener('submit', function (event) {
        updateAmount(customAmount.value);
        if (!amountHidden.value) {
            event.preventDefault();
            alert('Please enter a donation amount between RM1.00 and RM99,999,999.99.');
            customAmount.focus();
            return;
        }

        if (campaignSelect && !campaignSelect.value) {
            event.preventDefault();
            alert('Please select a campaign.');
            campaignSelect.focus();
        }
    });
});
</script>
