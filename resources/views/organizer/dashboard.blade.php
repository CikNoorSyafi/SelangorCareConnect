@extends('layouts.organizer')

@section('content')

<h1 class="text-2xl font-bold mb-2">Dashboard</h1>
<p class="text-gray-500 mb-6">Overview of your campaign performance</p>

<!-- STATS -->
<div class="grid grid-cols-4 gap-4 mb-6">

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Total Campaigns</p>
        <h2 class="text-xl font-bold">24</h2>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Active</p>
        <h2 class="text-xl font-bold">8</h2>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Volunteers</p>
        <h2 class="text-xl font-bold">120</h2>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Donations</p>
        <h2 class="text-xl font-bold">RM 5,000</h2>
    </div>

</div>

<!-- SIMPLE GRAPH -->
<div class="bg-white p-6 rounded shadow">
    <h2 class="font-semibold mb-4">Campaign Activity</h2>

    <canvas id="chart"></canvas>
</div>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May'],
        datasets: [{
            label: 'Campaigns',
            data: [2,5,3,8,6],
            borderWidth: 2
        }]
    }
});
</script>

@endsection