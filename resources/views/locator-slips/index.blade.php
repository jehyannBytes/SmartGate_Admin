@extends('layouts.app')
@section('title', 'Locator Slips')
@section('page-title', 'Locator Slips')
@section('page-subtitle', 'Off-campus assignment — review and approve')

@section('content')
<div class="space-y-4">
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

  <form method="GET" action="{{ route('locator-slips.index') }}" class="flex flex-wrap items-center gap-3">
    <div class="flex rounded-xl overflow-hidden border border-[#1e2a5e]/30">
      @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', '' => 'All'] as $val => $label)
        <button type="submit" name="status" value="{{ $val }}"
                class="px-4 py-2 text-sm font-medium transition-colors
                       {{ request('status', 'pending') === $val
                          ? 'bg-[#1e2a5e] text-white'
                          : 'bg-white text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
          {{ $label }}
        </button>
      @endforeach
    </div>
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search employee..."
           class="bg-white border border-slate-200 rounded-xl px-4 py-2
                  text-slate-900 placeholder-slate-400 text-sm w-56
                  focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40"/>
    <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Filter</button>
  </form>

  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl overflow-hidden shadow-sm">
    <table class="w-full">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-50">
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Location</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Schedule</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
          <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($slips as $slip)
          @php
            $statusColors = ['pending' => 'bg-amber-50 text-amber-600 border-amber-200','approved' => 'bg-emerald-50 text-emerald-600 border-emerald-200','rejected' => 'bg-red-50 text-red-600 border-red-200'];
          @endphp
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4">
              <p class="text-slate-900 text-sm font-medium">{{ $slip->employee?->full_name ?? 'Unknown' }}</p>
              <p class="text-slate-400 text-xs">{{ $slip->employee?->position }}</p>
            </td>
            <td class="px-6 py-4">
              <p class="text-slate-900 text-sm">{{ $slip->location }}</p>
              <p class="text-slate-400 text-xs truncate max-w-xs">{{ $slip->purpose }}</p>
            </td>
            <td class="px-6 py-4">
              <p class="text-slate-900 text-sm">{{ \Carbon\Carbon::parse($slip->start_time)->format('M d, h:i A') }}</p>
              <p class="text-slate-400 text-xs">to {{ \Carbon\Carbon::parse($slip->end_time)->format('M d, h:i A') }}</p>
            </td>
            <td class="px-6 py-4">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusColors[$slip->status] ?? '' }}">
                {{ ucfirst($slip->status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('locator-slips.show', $slip) }}" class="text-emerald-600 hover:underline text-sm font-medium">Review →</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">No locator slips found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($slips->hasPages())<div class="px-6 py-4 border-t border-slate-200">{{ $slips->links() }}</div>@endif
  </div>
</div>
@endsection