@extends('layouts.app')
 
@section('title', 'Analytics')
@section('page-title', 'Analytics Dashboard')
@section('page-subtitle', 'Attendance trends, department patterns, and device uptime')
 
@section('content')
<div class="space-y-4">
 
    {{-- Date Range Filter --}}
    <form method="GET" action="{{ route('analytics.index') }}" class="flex items-center gap-3">
        <select name="days" onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-xl px-4 py-2.5
                       text-slate-700 text-sm focus:outline-none focus:border-[#1e2a5e]/50">
            <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 days</option>
            <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 days</option>
            <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 days</option>
        </select>
    </form>
 
    {{-- Summary Cards --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-5 shadow-sm">
            <p class="text-slate-500 text-xs uppercase tracking-wider mb-2">Active Employees</p>
            <p class="text-3xl font-bold text-slate-900">{{ $totalEmployees }}</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 shadow-sm">
            <p class="text-emerald-600 text-xs uppercase tracking-wider mb-2">Total Scans</p>
            <p class="text-3xl font-bold text-slate-900">{{ $totalLogsRange }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 shadow-sm">
            <p class="text-blue-600 text-xs uppercase tracking-wider mb-2">Avg Daily Check-ins</p>
            <p class="text-3xl font-bold text-slate-900">{{ $avgDailyIn }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 shadow-sm">
            <p class="text-red-600 text-xs uppercase tracking-wider mb-2">Total Lates</p>
            <p class="text-3xl font-bold text-slate-900">{{ $totalLateRange }}</p>
        </div>
    </div>
 
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
 
        {{-- Attendance Trend --}}
        <div class="lg:col-span-2 bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
            <p class="text-slate-900 font-semibold text-sm mb-4">Daily Attendance Trend</p>
            <canvas id="trendChart" height="80"></canvas>
        </div>
 
        {{-- Late Patterns per Department --}}
        <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
            <p class="text-slate-900 font-semibold text-sm mb-4">Late Arrivals by Department</p>
            <canvas id="lateChart" height="200"></canvas>
        </div>
 
        {{-- Device Uptime --}}
        <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
            <p class="text-slate-900 font-semibold text-sm mb-4">Device Uptime</p>
            <canvas id="deviceChart" height="200"></canvas>
        </div>
 
        {{-- Log Type Breakdown --}}
        <div class="lg:col-span-2 bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
            <p class="text-slate-900 font-semibold text-sm mb-4">Scan Type Breakdown</p>
            <canvas id="logTypeChart" height="80"></canvas>
        </div>
    </div>
</div>
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    // Light-theme chart defaults: dark slate text, soft gray gridlines
    const chartTextColor = '#475569';        // slate-600
    const chartGridColor = 'rgba(15,23,42,0.06)'; // faint slate grid
    Chart.defaults.color = chartTextColor;
    Chart.defaults.borderColor = chartGridColor;
 
    // Trend line chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Check-ins',
                data: @json($trendData),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 2,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: chartGridColor } },
                y: { beginAtZero: true, grid: { color: chartGridColor } }
            }
        }
    });
 
    // Late by department bar chart
    new Chart(document.getElementById('lateChart'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($lateByDept)),
            datasets: [{
                label: 'Lates',
                data: @json(array_values($lateByDept)),
                backgroundColor: '#ef4444',
                borderRadius: 6,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: chartGridColor } }
            }
        }
    });
 
    // Device uptime doughnut
    new Chart(document.getElementById('deviceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Online', 'Offline'],
            datasets: [{
                data: [{{ $deviceOnline }}, {{ $deviceOffline }}],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 0,
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });
 
    // Log type breakdown bar chart
    new Chart(document.getElementById('logTypeChart'), {
        type: 'bar',
        data: {
            labels: @json($logTypeBreakdown->keys()),
            datasets: [{
                label: 'Count',
                data: @json($logTypeBreakdown->values()),
                backgroundColor: '#2563eb',
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: chartGridColor } },
                y: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
 
