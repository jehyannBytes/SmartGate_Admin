@extends('layouts.app')
@section('title', 'ATT Records')
@section('page-title', 'ATT Records')
@section('page-subtitle', 'Authority to Travel — review and approve requests')

@section('content')
<div class="space-y-4">

  {{-- Summary Cards --}}
  <div class="grid grid-cols-3 gap-4">
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-sm">
      <p class="text-amber-600 text-xs uppercase tracking-wider mb-2">Pending</p>
      <p class="text-3xl font-bold text-slate-900">{{ $pending }}</p>
    </div>
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 shadow-sm">
      <p class="text-emerald-600 text-xs uppercase tracking-wider mb-2">Approved</p>
      <p class="text-3xl font-bold text-slate-900">{{ $approved }}</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 shadow-sm">
      <p class="text-red-600 text-xs uppercase tracking-wider mb-2">Rejected</p>
      <p class="text-3xl font-bold text-slate-900">{{ $rejected }}</p>
    </div>
  </div>

  {{-- Action & Filter Bar --}}
  <div class="flex items-center justify-between gap-4 py-2">
    <!-- Search & Filter Controls -->
    <form method="GET" action="{{ route('att-records.index') }}" class="flex items-center gap-3">
      <div class="flex bg-slate-100 p-1 rounded-xl">
        <a href="{{ route('att-records.index', ['status' => 'pending']) }}" 
           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ request('status', 'pending') === 'pending' ? 'bg-[#1e2a5e] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
          Pending
        </a>
        <a href="{{ route('att-records.index', ['status' => 'approved']) }}" 
           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ request('status') === 'approved' ? 'bg-[#1e2a5e] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
          Approved
        </a>
        <a href="{{ route('att-records.index', ['status' => 'rejected']) }}" 
           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ request('status') === 'rejected' ? 'bg-[#1e2a5e] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
          Rejected
        </a>
        <a href="{{ route('att-records.index', ['status' => 'all']) }}" 
           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ request('status') === 'all' || request('status') === '' ? 'bg-[#1e2a5e] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
          All
        </a>
      </div>

      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employee..." class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40">
      <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors">
        Filter
      </button>
    </form>

    <!-- HR Action Button -->
    <a href="{{ route('att-records.create') }}" class="bg-[#1e2a5e] hover:bg-[#1e2a5e]/90 text-white font-semibold px-4 py-2 rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-sm">
      <span>+</span> File ATT Form
    </a>
  </div>

  {{-- Table --}}
  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl overflow-hidden shadow-sm">
    <table class="w-full">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-50">
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Destination / Purpose</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Travel Date</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Filed</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
          <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($records as $record)
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4">
              <p class="text-slate-900 text-sm font-medium">
                {{ $record->employee?->last_name }}, {{ $record->employee?->first_name }}
              </p>
              <p class="text-slate-400 text-xs">{{ $record->employee?->position }}</p>
            </td>
            <td class="px-6 py-4">
              <p class="text-slate-900 text-sm font-medium">{{ $record->destination }}</p>
              <p class="text-slate-400 text-xs truncate max-w-xs">{{ $record->purpose }}</p>
            </td>
            <td class="px-6 py-4">
              <p class="text-slate-900 text-sm">
                {{ \Carbon\Carbon::parse($record->departure_date ?? $record->travel_date)->format('M d, Y') }}
              </p>
              <p class="text-slate-400 text-xs">
                to {{ \Carbon\Carbon::parse($record->arrival_date ?? $record->return_date)->format('M d, Y') }}
              </p>
            </td>
            <td class="px-6 py-4">
              <p class="text-slate-600 text-sm">
                {{ $record->filed_at ? \Carbon\Carbon::parse($record->filed_at)->format('M d, Y') : '—' }}
              </p>
            </td>
            <td class="px-6 py-4">
              @php
                $statusColors = [
                  'pending'  => 'bg-amber-50 text-amber-600 border-amber-200',
                  'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                  'rejected' => 'bg-red-50 text-red-600 border-red-200',
                ];
              @endphp
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusColors[$record->status] ?? '' }}">
                {{ ucfirst($record->status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('att-records.show', $record) }}" class="text-emerald-600 hover:underline text-sm font-medium">
                Review →
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
              No ATT records found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
    @if($records->hasPages())
      <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
    @endif
  </div>
</div>
@endsection