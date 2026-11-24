@extends('layouts.donor')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900">Donation History</h1>
            <p class="mt-1 text-sm text-slate-500">Track your contributions and view your donation receipts.</p>
        </div>
        <a href="{{ route('donor.donation') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
            + New Donation
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ session('success') }}
        </div>
    @endif
    @if (session('info'))
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800" role="status">
            {{ session('info') }}
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Successful Donations</p>
            <p class="mt-2 text-2xl font-black text-red-600">RM {{ number_format((float) $totalDonated, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Transactions</p>
            <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format((int) $totalTransactions) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Latest Successful Donation</p>
            <p class="mt-2 text-2xl font-black text-slate-900">RM {{ number_format((float) $latestAmount, 2) }}</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <form method="GET" action="{{ route('donor.history') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto_auto]">
                <label class="sr-only" for="donation-history-search">Search donations</label>
                <input id="donation-history-search" type="search" name="search" value="{{ $search }}"
                    placeholder="Search campaign, fund or payment method..."
                    class="min-w-0 rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                <label class="sr-only" for="donation-history-status">Filter by status</label>
                <select id="donation-history-status" name="status" class="rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                    <option value="">All statuses</option>
                    <option value="Allocated" @selected($status === 'Allocated')>Completed</option>
                    <option value="Pending" @selected($status === 'Pending')>Pending</option>
                    <option value="Failed" @selected($status === 'Failed')>Failed</option>
                </select>
                <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Apply Filters</button>
                <a href="{{ route('donor.history') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Clear</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Fund / Campaign</th>
                        <th class="px-5 py-4">Amount</th>
                        <th class="px-5 py-4">Frequency</th>
                        <th class="px-5 py-4">Payment Method</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse ($history as $transaction)
                        @php
                            $campaignName = $transaction->campaign?->name
                                ?? ($transaction->campaign_id ? ($transaction->campaign_type ?: 'Campaign Fund') : 'General Fund');
                            $displayStatus = $transaction->status === 'Allocated' ? 'Completed' : $transaction->status;
                            $displayFrequency = ($transaction->frequency ?? 'one_time') === 'monthly' ? 'Monthly preference' : 'One-Time';
                            $displayDate = $transaction->created_at
                                ? $transaction->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y, h:i A')
                                : 'Date unavailable';
                            $isCompleted = $transaction->status === 'Allocated';
                        @endphp
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-5 py-4 text-slate-500">{{ $displayDate }}</td>
                            <td class="max-w-[280px] px-5 py-4">
                                <span class="block truncate font-semibold text-slate-900" title="{{ $campaignName }}">{{ $campaignName }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 font-bold text-slate-900">RM {{ number_format((float) $transaction->amount, 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $displayFrequency }}</td>
                            <td class="px-5 py-4">{{ $transaction->payment_method ?: 'Not selected' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $isCompleted ? 'bg-emerald-50 text-emerald-700' : ($transaction->status === 'Pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                    {{ $displayStatus }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button"
                                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700"
                                        data-receipt-trigger
                                        data-campaign="{{ $campaignName }}"
                                        data-donor="{{ $transaction->contributor ?: 'Donor' }}"
                                        data-method="{{ $transaction->payment_method ?: 'Not selected' }}"
                                        data-amount="{{ number_format((float) $transaction->amount, 2, '.', '') }}"
                                        data-status="{{ $displayStatus }}"
                                        data-date="{{ $displayDate }}"
                                        data-frequency="{{ $displayFrequency }}"
                                        data-email="{{ $transaction->receipt_email ?: '' }}"
                                        data-dedication="{{ $transaction->dedication ?: '' }}"
                                        data-completed="{{ $isCompleted ? '1' : '0' }}"
                                        data-download-url="{{ $isCompleted ? route('donor.receipt', $transaction->id) : '' }}">
                                        {{ $isCompleted ? 'View Receipt' : 'View Details' }}
                                    </button>
                                    @if ($transaction->status === 'Pending')
                                        <a href="{{ route('donor.payment.gateway', $transaction->id) }}" class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-amber-600">Continue Payment</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="font-semibold text-slate-700">No donation records found</p>
                                <p class="mt-1 text-sm text-slate-500">Try changing your filters or make a new donation.</p>
                                <a href="{{ route('donor.donation') }}" class="mt-4 inline-flex rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Make a Donation</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($history->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $history->links() }}</div>
        @endif
    </section>
</div>

{{-- Receipt modal: overlays the page instead of being appended after the donation list. --}}
<div id="donationReceiptModal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[2px]" data-modal-close></div>
    <div class="relative z-10 flex min-h-full items-center justify-center overflow-y-auto p-4 sm:p-6">
        <section role="dialog" aria-modal="true" aria-labelledby="donationReceiptTitle"
            class="my-auto w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                <div>
                    <p id="donationReceiptEyebrow" class="text-xs font-bold uppercase tracking-[0.16em] text-red-600">SelangorCareConnect+</p>
                    <h2 id="donationReceiptTitle" class="mt-1 text-2xl font-black text-slate-900">Donation Receipt</h2>
                    <p id="donationReceiptSubtitle" class="mt-1 text-sm text-slate-500">Thank you for supporting our community programmes.</p>
                </div>
                <button type="button" data-modal-close aria-label="Close receipt"
                    class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-5 px-6 py-6">
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 p-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Contribution Amount</p>
                        <p id="receiptAmount" class="mt-1 text-3xl font-black text-slate-900">RM 0.00</p>
                    </div>
                    <span id="receiptStatus" class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">Completed</span>
                </div>

                <dl class="grid gap-x-8 gap-y-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Donor Name</dt>
                        <dd id="receiptDonor" class="mt-1 break-words font-semibold text-slate-900">—</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fund / Campaign</dt>
                        <dd id="receiptCampaign" class="mt-1 break-words font-semibold text-slate-900">—</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payment Method</dt>
                        <dd id="receiptMethod" class="mt-1 break-words text-slate-700">—</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Contribution Frequency</dt>
                        <dd id="receiptFrequency" class="mt-1 text-slate-700">—</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Date &amp; Time</dt>
                        <dd id="receiptDate" class="mt-1 text-slate-700">—</dd>
                    </div>
                    <div id="receiptEmailWrap" class="hidden">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Receipt Email</dt>
                        <dd id="receiptEmail" class="mt-1 break-all text-slate-700">—</dd>
                    </div>
                    <div id="receiptDedicationWrap" class="hidden sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Dedication / Note</dt>
                        <dd id="receiptDedication" class="mt-1 whitespace-pre-wrap break-words text-slate-700">—</dd>
                    </div>
                </dl>

                <div id="receiptPendingNotice" class="hidden rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="status">
                    This contribution has not been completed. An official donation receipt will be available after successful payment.
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Close Receipt</button>
                <a id="receiptDownloadLink" href="#" class="hidden rounded-xl bg-slate-900 px-5 py-3 text-center text-sm font-semibold text-white transition hover:bg-slate-700">Download Receipt</a>
            </div>
        </section>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('donationReceiptModal');
    if (!modal) return;

    const downloadLink = document.getElementById('receiptDownloadLink');
    const pendingNotice = document.getElementById('receiptPendingNotice');
    let lastTrigger = null;

    const setText = (id, value, fallback = '—') => {
        const element = document.getElementById(id);
        if (element) element.textContent = value && value.trim() ? value : fallback;
    };

    const setOptionalField = (wrapId, valueId, value) => {
        const wrapper = document.getElementById(wrapId);
        const field = document.getElementById(valueId);
        const hasValue = Boolean(value && value.trim());
        wrapper?.classList.toggle('hidden', !hasValue);
        if (field) field.textContent = hasValue ? value : '';
    };

    const openModal = (trigger) => {
        lastTrigger = trigger;
        const data = trigger.dataset;
        const completed = data.completed === '1';

        document.getElementById('donationReceiptTitle').textContent = completed ? 'Donation Receipt' : 'Donation Details';
        document.getElementById('donationReceiptSubtitle').textContent = completed
            ? 'Thank you for supporting SelangorCareConnect+.'
            : 'This is a summary of your donation transaction.';
        setText('receiptAmount', `RM ${data.amount || '0.00'}`);
        setText('receiptDonor', data.donor);
        setText('receiptCampaign', data.campaign);
        setText('receiptMethod', data.method);
        setText('receiptFrequency', data.frequency);
        setText('receiptDate', data.date);
        setText('receiptStatus', data.status);
        setOptionalField('receiptEmailWrap', 'receiptEmail', data.email || '');
        setOptionalField('receiptDedicationWrap', 'receiptDedication', data.dedication || '');

        const status = document.getElementById('receiptStatus');
        status.className = completed
            ? 'inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700'
            : (data.status === 'Pending'
                ? 'inline-flex rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-700'
                : 'inline-flex rounded-full bg-rose-100 px-3 py-1.5 text-xs font-bold text-rose-700');

        downloadLink.href = data.downloadUrl || '#';
        downloadLink.classList.toggle('hidden', !completed || !data.downloadUrl);
        pendingNotice.classList.toggle('hidden', completed);

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        modal.querySelector('button[data-modal-close]')?.focus();
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        lastTrigger?.focus();
    };

    document.querySelectorAll('[data-receipt-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => openModal(trigger));
    });

    modal.addEventListener('click', (event) => {
        if (event.target.closest('[data-modal-close]')) closeModal();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
})();
</script>
@endsection
