@extends('layouts.app')
@section('title', 'HR Dashboard')
@section('page-title', 'HR Dashboard')
@section('page-subtitle', 'Attendance overview and pending approvals for today')
@section('content')
<div class="space-y-6">
  {{-- Stat Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-start gap-4">
      <div class="w-11 h-11 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M9 12h6m-6 4h6M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
      <div>
        <p class="text-slate-500 text-sm">Logs Today</p>
        <p class="text-2xl font-bold text-slate-900">{{ $total_logs_today ?? 0 }}</p>
        <p class="text-slate-400 text-xs mt-1">{{ $morning_in_today ?? 0 }} morning · {{ $afternoon_in_today ?? 0 }} afternoon</p>
      </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-start gap-4">
      <div class="w-11 h-11 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-3.13a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-slate-500 text-sm">Employees</p>
        <p class="text-2xl font-bold text-slate-900">{{ $total_employees ?? 0 }}</p>
        <p class="text-slate-400 text-xs mt-1">{{ $faculty_count ?? 0 }} faculty · {{ $non_teaching_count ?? 0 }} non-teaching</p>
      </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-start gap-4">
      <div class="w-11 h-11 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z"/>
        </svg>
      </div>
      <div>
        <p class="text-slate-500 text-sm">Pending ATT / Locator</p>
        <p class="text-2xl font-bold text-slate-900">{{ ($pending_att ?? 0) + ($pending_locator ?? 0) }}</p>
        <p class="text-slate-400 text-xs mt-1">{{ $pending_att ?? 0 }} ATT · {{ $pending_locator ?? 0 }} locator</p>
      </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-start gap-4">
      <div class="w-11 h-11 rounded-lg bg-red-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M12 8v8m-4-4h8M12 22a10 10 0 100-20 10 10 0 000 20z"/>
        </svg>
      </div>
      <div>
        <p class="text-slate-500 text-sm">Pending Leave</p>
        <p class="text-2xl font-bold text-slate-900">{{ $pending_leave ?? 0 }}</p>
        <p class="text-slate-400 text-xs mt-1">Awaiting your approval</p>
      </div>
    </div>
  </div>

  {{-- Quick Links --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <a href="{{ route('att-records.index') }}"
       class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:border-amber-300 transition-colors">
      <p class="text-amber-600 font-semibold mb-1">ATT Records</p>
      <p class="text-slate-400 text-sm">{{ $pending_att ?? 0 }} pending approval</p>
    </a>
    <a href="{{ route('locator-slips.index') }}"
       class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:border-blue-300 transition-colors">
      <p class="text-blue-600 font-semibold mb-1">Locator Slips</p>
      <p class="text-slate-400 text-sm">{{ $pending_locator ?? 0 }} pending approval</p>
    </a>
    <a href="{{ route('leave-requests.index') }}"
       class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:border-purple-300 transition-colors">
      <p class="text-purple-600 font-semibold mb-1">Leave Requests</p>
      <p class="text-slate-400 text-sm">{{ $pending_leave ?? 0 }} pending approval</p>
    </a>
  </div>
</div>
@endsection