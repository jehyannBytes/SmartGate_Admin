@extends('layouts.app')
@section('title', 'Attendance Logs')
@section('page-title', 'Attendance Logs')
@section('page-subtitle', 'View all attendance scan records')

@section('content')
<div class="space-y-4">

  {{-- Summary Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-5 shadow-sm">
      <p class="text-slate-500 text-xs uppercase tracking-wider mb-2">Total Logs</p>
      <p class="text-3xl font-bold text-slate-900">{{ $summary['total'] }}</p>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 shadow-sm">
      <p class="text-blue-600 text-xs uppercase tracking-wider mb-2">Morning In</p>
      <p class="text-3xl font-bold text-slate-900">{{ $summary['morning_in'] }}</p>
      <p class="text-slate-400 text-xs mt-1">Out: {{ $summary['morning_out'] }}</p>
    </div>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-sm">
      <p class="text-amber-600 text-xs uppercase tracking-wider mb-2">Afternoon In</p>
      <p class="text-3xl font-bold text-slate-900">{{ $summary['afternoon_in'] }}</p>
      <p class="text-slate-400 text-xs mt-1">Out: {{ $summary['afternoon_out'] }}</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 shadow-sm">
      <p class="text-red-600 text-xs uppercase tracking-wider mb-2">Unverified</p>
      <p class="text-3xl font-bold text-slate-900">{{ $summary['unverified'] }}</p>
      <p class="text-slate-400 text-xs mt-1">Pending sync: {{ $summary['pending_sync'] }}</p>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('attendance.index') }}"
        class="flex flex-wrap items-center gap-3">

    {{-- Date Filter --}}
    <div class="flex rounded-xl overflow-hidden border border-[#1e2a5e]/30">
      @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'custom' => 'Custom'] as $val => $label)
        <button type="submit" name="filter" value="{{ $val }}"
                class="px-4 py-2 text-sm font-medium transition-colors
                       {{ $filter === $val
                          ? 'bg-[#1e2a5e] text-white'
                          : 'bg-white text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
          {{ $label }}
        </button>
      @endforeach
    </div>

    {{-- Custom Date Range --}}
    @if($filter === 'custom')
      <input type="date" name="date_from" value="{{ request('date_from') }}"
             class="bg-white border border-slate-200 rounded-xl px-4 py-2
                    text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40"/>
      <span class="text-slate-400 text-sm">to</span>
      <input type="date" name="date_to" value="{{ request('date_to') }}"
             class="bg-white border border-slate-200 rounded-xl px-4 py-2
                    text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40"/>
    @endif

    {{-- Search --}}
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search employee..."
           class="bg-white border border-slate-200 rounded-xl px-4 py-2
                  text-slate-700 placeholder-slate-400 text-sm w-56
                  focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40"/>

    {{-- Log Type --}}
    <select name="log_type"
            class="bg-white border border-slate-200 rounded-xl px-4 py-2
                   text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40">
      <option value="">All Log Types</option>
      <option value="Morning In"    {{ request('log_type') === 'Morning In'    ? 'selected' : '' }}>Morning In</option>
      <option value="Morning Out"   {{ request('log_type') === 'Morning Out'   ? 'selected' : '' }}>Morning Out</option>
      <option value="Afternoon In"  {{ request('log_type') === 'Afternoon In'  ? 'selected' : '' }}>Afternoon In</option>
      <option value="Afternoon Out" {{ request('log_type') === 'Afternoon Out' ? 'selected' : '' }}>Afternoon Out</option>
    </select>

    {{-- Face Verified --}}
    <select name="face_verified"
            class="bg-white border border-slate-200 rounded-xl px-4 py-2
                   text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40">
      <option value="">All</option>
      <option value="1" {{ request('face_verified') === '1' ? 'selected' : '' }}>Verified</option>
      <option value="0" {{ request('face_verified') === '0' ? 'selected' : '' }}>Unverified</option>
    </select>

    <button type="submit"
            class="bg-[#1e2a5e] hover:bg-[#141d47] text-white px-4 py-2
                   rounded-xl text-sm font-medium transition-colors">
      Apply
    </button>

    @if(request()->hasAny(['search', 'log_type', 'face_verified', 'sync_status']))
      <a href="{{ route('attendance.index') }}"
         class="text-slate-400 hover:text-slate-900 text-sm transition-colors">Clear</a>
    @endif
  </form>

  {{-- Table --}}
  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl overflow-hidden shadow-sm">
    <table class="w-full">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-50">
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Log Type</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Time</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Face</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sync</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($logs as $log)
          @php
            $typeColors = [
              'Morning In'    => 'bg-blue-50 text-blue-600 border-blue-200',
              'Morning Out'   => 'bg-sky-50 text-sky-600 border-sky-200',
              'Afternoon In'  => 'bg-amber-50 text-amber-600 border-amber-200',
              'Afternoon Out' => 'bg-orange-50 text-orange-600 border-orange-200',
            ];
            $color = $typeColors[$log->log_type] ?? 'bg-slate-100 text-slate-500 border-slate-200';
          @endphp
          <tr class="hover:bg-slate-50 transition-colors">
            {{-- Employee --}}
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200
                            flex items-center justify-center shrink-0">
                  <span class="text-emerald-600 text-xs font-bold">
                    {{ strtoupper(substr($log->employee?->first_name ?? '?', 0, 1)) }}
                  </span>
                </div>
                <div>
                  <p class="text-slate-900 text-sm font-medium">
                    {{ $log->employee?->full_name ?? 'Unknown' }}
                  </p>
                  <p class="text-slate-400 text-xs">{{ $log->employee?->position ?? '' }}</p>
                </div>
              </div>
            </td>

            {{-- Log Type --}}
            <td class="px-6 py-4">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $color }}">
                {{ $log->log_type }}
              </span>
            </td>

            {{-- Time --}}
            <td class="px-6 py-4">
              <p class="text-slate-900 text-sm font-medium">
                {{ \Carbon\Carbon::parse($log->scanned_at)->format('h:i:s A') }}
              </p>
              <p class="text-slate-400 text-xs">
                {{ \Carbon\Carbon::parse($log->scanned_at)->format('M d, Y') }}
              </p>
            </td>

            {{-- Face Verified --}}
            <td class="px-6 py-4">
              @if($log->face_verified)
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              @else
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              @endif
            </td>

            {{-- Sync Status --}}
            <td class="px-6 py-4">
              @if($log->sync_status === 'synced')
                <span class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                       viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0
                             001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25
                             0 00-10.233 2.33A4.502 4.502 0 002.25 15z"/>
                  </svg>
                  Synced
                </span>
              @else
                <span class="flex items-center gap-1.5 text-xs font-semibold text-amber-600">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                       viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5
                             4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32
                             3.75 3.75 0 013.548 3.848A4.5 4.5 0 0117.25 19.5H6.75z"/>
                  </svg>
                  Pending
                </span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
              No attendance logs found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Pagination --}}
    @if($logs->hasPages())
      <div class="px-6 py-4 border-t border-slate-200">
        {{ $logs->links() }}
      </div>
    @endif
  </div>
</div>
@endsection