@extends('layouts.app')
@section('title', 'ICTMO Dashboard')
@section('page-title', 'ICTMO Dashboard')
@section('page-subtitle', 'System health, device status, and employee management overview')
@section('content')
<div class="space-y-6">
  {{-- Stat Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
      <p class="text-white/50 text-xs font-medium uppercase tracking-wider mb-3">Total Employees</p>
      <p class="text-3xl font-bold text-white">{{ $total_employees ?? 0 }}</p>
      <p class="text-white/30 text-xs mt-1">{{ $faculty_count ?? 0 }} faculty · {{ $non_teaching_count ?? 0 }} non-teaching</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
      <p class="text-white/50 text-xs font-medium uppercase tracking-wider mb-3">Devices</p>
      <p class="text-3xl font-bold text-white">{{ $active_devices ?? 0 }}</p>
      <p class="text-white/30 text-xs mt-1">{{ $total_devices ?? 0 }} total · {{ $offline_devices ?? 0 }} offline</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
      <p class="text-white/50 text-xs font-medium uppercase tracking-wider mb-3">Pending Sync</p>
      <p class="text-3xl font-bold text-white">{{ $pending_sync ?? 0 }}</p>
      <p class="text-white/30 text-xs mt-1">{{ $synced_today ?? 0 }} synced today</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
      <p class="text-white/50 text-xs font-medium uppercase tracking-wider mb-3">Notifications</p>
      <p class="text-3xl font-bold text-white">{{ $unread_notifs ?? 0 }}</p>
      <p class="text-white/30 text-xs mt-1">Unread alerts</p>
    </div>
  </div>

  {{-- Quick Links --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <a href="{{ route('employees.index') }}" class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:border-blue-500/30 transition-colors">
      <p class="text-blue-400 font-semibold mb-1">Manage Employees</p>
      <p class="text-white/30 text-sm">{{ $total_employees ?? 0 }} active employees</p>
    </a>
    <a href="{{ route('devices.index') }}" class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:border-[#4CAF82]/30 transition-colors">
      <p class="text-[#4CAF82] font-semibold mb-1">Manage Devices</p>
      <p class="text-white/30 text-sm">{{ $active_devices ?? 0 }} active · {{ $offline_devices ?? 0 }} offline</p>
    </a>
    <a href="{{ route('notifications.index') }}" class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:border-purple-500/30 transition-colors">
      <p class="text-purple-400 font-semibold mb-1">Notifications</p>
      <p class="text-white/30 text-sm">{{ $unread_notifs ?? 0 }} unread alerts</p>
    </a>
  </div>
</div>
@endsection
