@extends('layouts.app')
@section('title', 'Review Leave Request')
@section('page-title', 'Review Leave Request')
@section('page-subtitle', $leaveRequest->employee?->full_name)

@section('content')
<div class="max-w-3xl space-y-6">
  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center gap-4 mb-6">
      <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center">
        <span class="text-emerald-600 font-bold">{{ strtoupper(substr($leaveRequest->employee?->first_name ?? '?', 0, 1)) }}</span>
      </div>
      <div>
        <p class="text-slate-900 font-semibold">{{ $leaveRequest->employee?->full_name ?? 'Unknown' }}</p>
        <p class="text-slate-400 text-sm">{{ $leaveRequest->employee?->position }} · {{ $leaveRequest->employee?->department }}</p>
      </div>
      @php $statusColors = ['pending' => 'bg-amber-50 text-amber-600 border-amber-200','approved' => 'bg-emerald-50 text-emerald-600 border-emerald-200','rejected' => 'bg-red-50 text-red-600 border-red-200']; @endphp
      <span class="ml-auto text-xs font-semibold px-3 py-1.5 rounded-full border {{ $statusColors[$leaveRequest->status] ?? '' }}">{{ ucfirst($leaveRequest->status) }}</span>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><p class="text-slate-400 text-xs uppercase tracking-wider">Leave Type</p><p class="text-slate-900 text-sm mt-0.5 capitalize">{{ $leaveRequest->leave_type }}</p></div>
      <div><p class="text-slate-400 text-xs uppercase tracking-wider">Total Days</p><p class="text-slate-900 text-sm mt-0.5">{{ $leaveRequest->total_days }} day(s)</p></div>
      <div><p class="text-slate-400 text-xs uppercase tracking-wider">Start Date</p><p class="text-slate-900 text-sm mt-0.5">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('M d, Y') }}</p></div>
      <div><p class="text-slate-400 text-xs uppercase tracking-wider">End Date</p><p class="text-slate-900 text-sm mt-0.5">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('M d, Y') }}</p></div>
      <div><p class="text-slate-400 text-xs uppercase tracking-wider">Filed At</p><p class="text-slate-900 text-sm mt-0.5">{{ \Carbon\Carbon::parse($leaveRequest->filed_at)->format('M d, Y h:i A') }}</p></div>
    </div>
  </div>

  @if($leaveRequest->image_url)
    <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
      <p class="text-slate-500 text-xs uppercase tracking-wider mb-3">Uploaded Form</p>
      <img src="{{ $leaveRequest->image_url }}" alt="Leave Form" class="w-full rounded-xl border border-slate-200 max-h-96 object-contain bg-slate-50"/>
    </div>
  @endif

  @if($leaveRequest->status === 'pending')
    <div class="flex items-center gap-4">
      <form method="POST" action="{{ route('leave-requests.update', $leaveRequest) }}">
        @csrf @method('PATCH')
        <input type="hidden" name="action" value="approved"/>
        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-colors">✓ Approve Leave</button>
      </form>
      <form method="POST" action="{{ route('leave-requests.update', $leaveRequest) }}" onsubmit="return confirm('Reject this Leave Request?')">
        @csrf @method('PATCH')
        <input type="hidden" name="action" value="rejected"/>
        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-semibold px-6 py-3 rounded-xl text-sm transition-colors">✗ Reject</button>
      </form>
      <a href="{{ route('leave-requests.index') }}" class="text-slate-400 hover:text-slate-900 text-sm transition-colors ml-auto">← Back</a>
    </div>
  @else
    <a href="{{ route('leave-requests.index') }}" class="inline-block text-slate-400 hover:text-slate-900 text-sm transition-colors">← Back to List</a>
  @endif
</div>
@endsection