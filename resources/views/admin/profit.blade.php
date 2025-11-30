@extends('layouts.base')
@section('title','Profit Graph')
@section('content')
<x-header />
<div class="min-h-screen p-6 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Profit (Gross Revenue)</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:underline">Back to Dashboard</a>
    </div>
    <div class="bg-white p-6 shadow rounded">
        <canvas id="profitChart" height="120"></canvas>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
fetch('{{ route('admin.profit.data') }}')
  .then(r => r.json())
  .then(data => {
    const labels = data.days.map(d => d.date);
    const values = data.days.map(d => d.total);
    new Chart(document.getElementById('profitChart'), {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Daily Gross Revenue (₱)',
          data: values,
          borderColor: '#6366F1',
          backgroundColor: 'rgba(99,102,241,0.15)',
          tension: .25,
          fill: true,
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  });
</script>
@endsection
