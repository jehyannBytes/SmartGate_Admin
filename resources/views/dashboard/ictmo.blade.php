@extends('layouts.app')
@section('title', 'ICTMO Dashboard')
@section('page-title', 'ICTMO Dashboard')
@section('page-subtitle', 'System health, device status, and employee management overview')
@section('content')
<div class="space-y-6">
  {{-- Stat Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="bg-white border border-[#1e2a5e]/30 rounded-xl p-5 shadow-sm flex items-start gap-4">
      <div class="w-11 h-11 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-3.13a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-slate-500 text-sm">Total Employees</p>
        <p class="text-2xl font-bold text-slate-900">{{ $total_employees ?? 0 }}</p>
        <p class="text-slate-400 text-xs mt-1">{{ $faculty_count ?? 0 }} faculty · {{ $non_teaching_count ?? 0 }} non-teaching</p>
      </div>
    </div>

    <div class="bg-white border border-[#1e2a5e]/30 rounded-xl p-5 shadow-sm flex items-start gap-4">
      <div class="w-11 h-11 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M9.75 17L4 11.5 5.5 10l4.25 4.25L18.5 5.5 20 7 9.75 17z"/>
        </svg>
      </div>
      <div>
        <p class="text-slate-500 text-sm">Devices</p>
        <p class="text-2xl font-bold text-slate-900">{{ $active_devices ?? 0 }}</p>
        <p class="text-slate-400 text-xs mt-1">{{ $total_devices ?? 0 }} total · {{ $offline_devices ?? 0 }} offline</p>
      </div>
    </div>

    <div class="bg-white border border-[#1e2a5e]/30 rounded-xl p-5 shadow-sm flex items-start gap-4">
      <div class="w-11 h-11 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M4 4v5h5M20 20v-5h-5M4 9a8 8 0 0114-4.3M20 15a8 8 0 01-14 4.3"/>
        </svg>
      </div>
      <div>
        <p class="text-slate-500 text-sm">Pending Sync</p>
        <p class="text-2xl font-bold text-slate-900">{{ $pending_sync ?? 0 }}</p>
        <p class="text-slate-400 text-xs mt-1">{{ $synced_today ?? 0 }} synced today</p>
      </div>
    </div>

    <div class="bg-white border border-[#1e2a5e]/30 rounded-xl p-5 shadow-sm flex items-start gap-4">
      <div class="w-11 h-11 rounded-lg bg-purple-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
      </div>
      <div>
        <p class="text-slate-500 text-sm">Notifications</p>
        <p class="text-2xl font-bold text-slate-900">{{ $unread_notifs ?? 0 }}</p>
        <p class="text-slate-400 text-xs mt-1">Unread alerts</p>
      </div>
    </div>
  </div>

  {{-- Quick Links --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <a href="{{ route('employees.index') }}"
       class="bg-white border border-[#1e2a5e]/30 rounded-xl p-6 shadow-sm hover:border-blue-300 transition-colors">
      <p class="text-blue-600 font-semibold mb-1">Manage Employees</p>
      <p class="text-slate-400 text-sm">{{ $total_employees ?? 0 }} active employees</p>
    </a>
    <a href="{{ route('devices.index') }}"
       class="bg-white border border-[#1e2a5e]/30 rounded-xl p-6 shadow-sm hover:border-emerald-300 transition-colors">
      <p class="text-emerald-600 font-semibold mb-1">Manage Devices</p>
      <p class="text-slate-400 text-sm">{{ $active_devices ?? 0 }} active · {{ $offline_devices ?? 0 }} offline</p>
    </a>
    <a href="{{ route('notifications.index') }}"
       class="bg-white border border-[#1e2a5e]/30 rounded-xl p-6 shadow-sm hover:border-purple-300 transition-colors">
      <p class="text-purple-600 font-semibold mb-1">Notifications</p>
      <p class="text-slate-400 text-sm">{{ $unread_notifs ?? 0 }} unread alerts</p>
    </a>
  </div>
</div>
@endsection