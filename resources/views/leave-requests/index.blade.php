@extends('layouts.app')
@section('title', 'Leave Requests')
@section('page-title', 'Leave Requests')
@section('page-subtitle', 'Employee leave management — review and approve')

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

  <form method="GET" action="{{ route('leave-requests.index') }}" class="flex flex-wrap items-center gap-3">
    <div class="flex rounded-xl overflow-hidden border border-white/10">
      @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', '' => 'All'] as $val => $label)
        <button type="submit" name="status" value="{{ $val }}"
                class="px-4 py-2 text-sm font-medium transition-colors
                       {{ request('status', 'pending') === $val ? 'bg-[#4CAF82] text-white' : 'bg-white/5 text-white/50 hover:text-white hover:bg-white/10' }}">
          {{ $label }}
        </button>
      @endforeach
    </div>
    <select name="leave_type" class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#4CAF82]">
      <option value="">All Leave Types</option>
      @foreach(['sick', 'vacation', 'emergency', 'maternity', 'paternity', 'others'] as $type)
        <option value="{{ $type }}" {{ request('leave_type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
      @endforeach
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employee..."
           class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-white/30 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-[#4CAF82]"/>
    <button type="submit" class="bg-white/10 hover:bg-white/15 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">Filter</button>
  </form>

  <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
    <table class="w-full">
      <thead>
        <tr class="border-b border-white/10 bg-white/3">
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Employee</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Leave Type</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Duration</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Filed</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Status</th>
          <th class="text-right px-6 py-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/5">
        @forelse($requests as $req)
          @php $statusColors = ['pending' => 'bg-yellow-500/15 text-yellow-400 border-yellow-500/20','approved' => 'bg-[#4CAF82]/15 text-[#4CAF82] border-[#4CAF82]/20','rejected' => 'bg-red-500/15 text-red-400 border-red-500/20']; @endphp
          <tr class="hover:bg-white/3 transition-colors">
            <td class="px-6 py-4">
              <p class="text-white text-sm font-medium">{{ $req->employee?->full_name ?? 'Unknown' }}</p>
              <p class="text-white/30 text-xs">{{ $req->employee?->position }}</p>
            </td>
            <td class="px-6 py-4"><span class="text-white/70 text-sm capitalize">{{ $req->leave_type }}</span></td>
            <td class="px-6 py-4">
              <p class="text-white text-sm">{{ \Carbon\Carbon::parse($req->start_date)->format('M d') }} – {{ \Carbon\Carbon::parse($req->end_date)->format('M d, Y') }}</p>
              <p class="text-white/30 text-xs">{{ $req->total_days }} day(s)</p>
            </td>
            <td class="px-6 py-4"><p class="text-white/60 text-sm">{{ \Carbon\Carbon::parse($req->filed_at)->format('M d, Y') }}</p></td>
            <td class="px-6 py-4">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusColors[$req->status] ?? '' }}">{{ ucfirst($req->status) }}</span>
            </td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('leave-requests.show', $req) }}" class="text-[#4CAF82] hover:underline text-sm font-medium">Review →</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-6 py-12 text-center text-white/30 text-sm">No leave requests found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($requests->hasPages())<div class="px-6 py-4 border-t border-white/10">{{ $requests->links() }}</div>@endif
  </div>
</div>
@endsection
