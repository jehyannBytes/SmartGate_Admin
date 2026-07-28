@extends('layouts.app')
@section('title', 'HR Dashboard')
@section('page-title', 'HR Dashboard')
@section('page-subtitle', 'Attendance overview and pending approvals for today')
@section('content')
<div class="space-y-6">
  {{-- Stat Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
      <p class="text-white/50 text-xs font-medium uppercase tracking-wider mb-3">Logs Today</p>
      <p class="text-3xl font-bold text-white">{{ $total_logs_today ?? 0 }}</p>
      <p class="text-white/30 text-xs mt-1">{{ $morning_in_today ?? 0 }} morning · {{ $afternoon_in_today ?? 0 }} afternoon</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
      <p class="text-white/50 text-xs font-medium uppercase tracking-wider mb-3">Employees</p>
      <p class="text-3xl font-bold text-white">{{ $total_employees ?? 0 }}</p>
      <p class="text-white/30 text-xs mt-1">{{ $faculty_count ?? 0 }} faculty · {{ $non_teaching_count ?? 0 }} non-teaching</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
      <p class="text-white/50 text-xs font-medium uppercase tracking-wider mb-3">Pending ATT / Locator</p>
      <p class="text-3xl font-bold text-white">{{ ($pending_att ?? 0) + ($pending_locator ?? 0) }}</p>
      <p class="text-white/30 text-xs mt-1">{{ $pending_att ?? 0 }} ATT · {{ $pending_locator ?? 0 }} locator</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
      <p class="text-white/50 text-xs font-medium uppercase tracking-wider mb-3">Pending Leave</p>
      <p class="text-3xl font-bold text-white">{{ $pending_leave ?? 0 }}</p>
      <p class="text-white/30 text-xs mt-1">Awaiting your approval</p>
    </div>
  </div>

  {{-- Quick Links --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <a href="{{ route('att-records.index') }}" class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:border-yellow-500/30 transition-colors">
      <p class="text-yellow-400 font-semibold mb-1">ATT Records</p>
      <p class="text-white/30 text-sm">{{ $pending_att ?? 0 }} pending approval</p>
    </a>
    <a href="{{ route('locator-slips.index') }}" class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:border-blue-500/30 transition-colors">
      <p class="text-blue-400 font-semibold mb-1">Locator Slips</p>
      <p class="text-white/30 text-sm">{{ $pending_locator ?? 0 }} pending approval</p>
    </a>
    <a href="{{ route('leave-requests.index') }}" class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:border-purple-500/30 transition-colors">
      <p class="text-purple-400 font-semibold mb-1">Leave Requests</p>
      <p class="text-white/30 text-sm">{{ $pending_leave ?? 0 }} pending approval</p>
    </a>
  </div>
</div>
@endsection
