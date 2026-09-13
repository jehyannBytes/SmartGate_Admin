@extends('layouts.app')
@section('title', 'Review ATT Record')
@section('page-title', 'Review ATT Record')
@section('page-subtitle', 'Authority to Travel — ' . $attRecord->employee?->full_name)

@section('content')
<div class="max-w-3xl space-y-6">

  {{-- Employee Info --}}
  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center gap-4 mb-6">
      <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200
                  flex items-center justify-center">
        <span class="text-emerald-600 font-bold">
          {{ strtoupper(substr($attRecord->employee?->first_name ?? '?', 0, 1)) }}
        </span>
      </div>
      <div>
        <p class="text-slate-900 font-semibold">{{ $attRecord->employee?->full_name ?? 'Unknown' }}</p>
        <p class="text-slate-400 text-sm">{{ $attRecord->employee?->position }} · {{ $attRecord->employee?->department }}</p>
      </div>
      @php
        $statusColors = [
          'pending'  => 'bg-amber-50 text-amber-600 border-amber-200',
          'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
          'rejected' => 'bg-red-50 text-red-600 border-red-200',
        ];
      @endphp
      <span class="ml-auto text-xs font-semibold px-3 py-1.5 rounded-full border
                   {{ $statusColors[$attRecord->status] ?? '' }}">
        {{ ucfirst($attRecord->status) }}
      </span>
    </div>

    {{-- Details --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <p class="text-slate-400 text-xs uppercase tracking-wider">Destination</p>
        <p class="text-slate-900 text-sm mt-0.5">{{ $attRecord->destination }}</p>
      </div>
      <div>
        <p class="text-slate-400 text-xs uppercase tracking-wider">Purpose</p>
        <p class="text-slate-900 text-sm mt-0.5">{{ $attRecord->purpose }}</p>
      </div>
      <div>
        <p class="text-slate-400 text-xs uppercase tracking-wider">Travel Date</p>
        <p class="text-slate-900 text-sm mt-0.5">{{ \Carbon\Carbon::parse($attRecord->travel_date)->format('M d, Y') }}</p>
      </div>
      <div>
        <p class="text-slate-400 text-xs uppercase tracking-wider">Return Date</p>
        <p class="text-slate-900 text-sm mt-0.5">{{ \Carbon\Carbon::parse($attRecord->return_date)->format('M d, Y') }}</p>
      </div>
      <div>
        <p class="text-slate-400 text-xs uppercase tracking-wider">Filed At</p>
        <p class="text-slate-900 text-sm mt-0.5">{{ \Carbon\Carbon::parse($attRecord->filed_at)->format('M d, Y h:i A') }}</p>
      </div>
      @if($attRecord->approved_at)
      <div>
        <p class="text-slate-400 text-xs uppercase tracking-wider">Reviewed At</p>
        <p class="text-slate-900 text-sm mt-0.5">{{ \Carbon\Carbon::parse($attRecord->approved_at)->format('M d, Y h:i A') }}</p>
      </div>
      @endif
    </div>
  </div>

  {{-- Uploaded Image --}}
  @if($attRecord->image_url)
    <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
      <p class="text-slate-500 text-xs uppercase tracking-wider mb-3">Uploaded Form</p>
      <img src="{{ $attRecord->image_url }}" alt="ATT Form"
           class="w-full rounded-xl border border-slate-200 max-h-96 object-contain bg-slate-50"/>
    </div>
  @else
    <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
      <p class="text-slate-400 text-sm text-center py-4">No image uploaded.</p>
    </div>
  @endif

  {{-- Approve / Reject Buttons --}}
  @if($attRecord->status === 'pending')
    <div class="flex items-center gap-4">
      <form method="POST" action="{{ route('att-records.update', $attRecord) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="action" value="approved"/>
        <button type="submit"
                class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold
                       px-6 py-3 rounded-xl text-sm transition-colors">
          ✓ Approve ATT Record
        </button>
      </form>
      <form method="POST" action="{{ route('att-records.update', $attRecord) }}"
            onsubmit="return confirm('Reject this ATT Record?')">
        @csrf
        @method('PATCH')
        <input type="hidden" name="action" value="rejected"/>
        <button type="submit"
                class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200
                       font-semibold px-6 py-3 rounded-xl text-sm transition-colors">
          ✗ Reject
        </button>
      </form>
      <a href="{{ route('att-records.index') }}"
         class="text-slate-400 hover:text-slate-900 text-sm transition-colors ml-auto">
        ← Back to List
      </a>
    </div>
  @else
    <a href="{{ route('att-records.index') }}"
       class="inline-block text-slate-400 hover:text-slate-900 text-sm transition-colors">
      ← Back to List
    </a>
  @endif
</div>
@endsection