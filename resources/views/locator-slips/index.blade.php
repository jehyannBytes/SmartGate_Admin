@extends('layouts.app')
@section('title', 'Locator Slips')
@section('page-title', 'Locator Slips')
@section('page-subtitle', 'Off-campus assignment — review and approve')

@section('content')
<div class="space-y-4">
  <div class="grid grid-cols-3 gap-4">
    <div class="bg-yellow-500/5 border border-yellow-500/20 rounded-2xl p-5">
      <p class="text-yellow-400 text-xs uppercase tracking-wider mb-2">Pending</p>
      <p class="text-3xl font-bold text-white">{{ $pending }}</p>
    </div>
    <div class="bg-[#4CAF82]/5 border border-[#4CAF82]/20 rounded-2xl p-5">
      <p class="text-[#4CAF82] text-xs uppercase tracking-wider mb-2">Approved</p>
      <p class="text-3xl font-bold text-white">{{ $approved }}</p>
    </div>
    <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-5">
      <p class="text-red-400 text-xs uppercase tracking-wider mb-2">Rejected</p>
      <p class="text-3xl font-bold text-white">{{ $rejected }}</p>
    </div>
  </div>

  <form method="GET" action="{{ route('locator-slips.index') }}" class="flex flex-wrap items-center gap-3">
    <div class="flex rounded-xl overflow-hidden border border-white/10">
      @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', '' => 'All'] as $val => $label)
        <button type="submit" name="status" value="{{ $val }}"
                class="px-4 py-2 text-sm font-medium transition-colors
                       {{ request('status', 'pending') === $val
                          ? 'bg-[#4CAF82] text-white'
                          : 'bg-white/5 text-white/50 hover:text-white hover:bg-white/10' }}">
          {{ $label }}
        </button>
      @endforeach
    </div>
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search employee..."
           class="bg-white/5 border border-white/10 rounded-xl px-4 py-2
                  text-white placeholder-white/30 text-sm w-56
                  focus:outline-none focus:ring-2 focus:ring-[#4CAF82]"/>
    <button type="submit" class="bg-white/10 hover:bg-white/15 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">Filter</button>
  </form>

  <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
    <table class="w-full">
      <thead>
        <tr class="border-b border-white/10 bg-white/3">
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Employee</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Location</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Schedule</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Status</th>
          <th class="text-right px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/5">
        @forelse($slips as $slip)
          @php
            $statusColors = ['pending' => 'bg-yellow-500/15 text-yellow-400 border-yellow-500/20','approved' => 'bg-[#4CAF82]/15 text-[#4CAF82] border-[#4CAF82]/20','rejected' => 'bg-red-500/15 text-red-400 border-red-500/20'];
          @endphp
          <tr class="hover:bg-white/3 transition-colors">
            <td class="px-6 py-4">
              <p class="text-white text-sm font-medium">{{ $slip->employee?->full_name ?? 'Unknown' }}</p>
              <p class="text-white/30 text-xs">{{ $slip->employee?->position }}</p>
            </td>
            <td class="px-6 py-4">
              <p class="text-white text-sm">{{ $slip->location }}</p>
              <p class="text-white/30 text-xs truncate max-w-xs">{{ $slip->purpose }}</p>
            </td>
            <td class="px-6 py-4">
              <p class="text-white text-sm">{{ \Carbon\Carbon::parse($slip->start_time)->format('M d, h:i A') }}</p>
              <p class="text-white/30 text-xs">to {{ \Carbon\Carbon::parse($slip->end_time)->format('M d, h:i A') }}</p>
            </td>
            <td class="px-6 py-4">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusColors[$slip->status] ?? '' }}">
                {{ ucfirst($slip->status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('locator-slips.show', $slip) }}" class="text-[#4CAF82] hover:underline text-sm font-medium">Review →</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-6 py-12 text-center text-white/30 text-sm">No locator slips found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($slips->hasPages())<div class="px-6 py-4 border-t border-white/10">{{ $slips->links() }}</div>@endif
  </div>
</div>
@endsection
