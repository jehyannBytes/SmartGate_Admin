@extends('layouts.app')
@section('title', 'Review ATT Record')
@section('page-title', 'Review ATT Record')
@section('page-subtitle', 'Authority to Travel — ' . $attRecord->employee?->full_name)

@section('content')
<div class="max-w-3xl space-y-6">

  {{-- Employee Info --}}
  <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
    <div class="flex items-center gap-4 mb-6">
      <div class="w-12 h-12 rounded-full bg-[#4CAF82]/15 border border-[#4CAF82]/30
                  flex items-center justify-center">
        <span class="text-[#4CAF82] font-bold">
          {{ strtoupper(substr($attRecord->employee?->first_name ?? '?', 0, 1)) }}
        </span>
      </div>
      <div>
        <p class="text-white font-semibold">{{ $attRecord->employee?->full_name ?? 'Unknown' }}</p>
        <p class="text-white/40 text-sm">{{ $attRecord->employee?->position }} · {{ $attRecord->employee?->department }}</p>
      </div>
      @php
        $statusColors = [
          'pending'  => 'bg-yellow-500/15 text-yellow-400 border-yellow-500/20',
          'approved' => 'bg-[#4CAF82]/15 text-[#4CAF82] border-[#4CAF82]/20',
          'rejected' => 'bg-red-500/15 text-red-400 border-red-500/20',
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
        <p class="text-white/40 text-xs uppercase tracking-wider">Destination</p>
        <p class="text-white text-sm mt-0.5">{{ $attRecord->destination }}</p>
      </div>
      <div>
        <p class="text-white/40 text-xs uppercase tracking-wider">Purpose</p>
        <p class="text-white text-sm mt-0.5">{{ $attRecord->purpose }}</p>
      </div>
      <div>
        <p class="text-white/40 text-xs uppercase tracking-wider">Travel Date</p>
        <p class="text-white text-sm mt-0.5">{{ \Carbon\Carbon::parse($attRecord->travel_date)->format('M d, Y') }}</p>
      </div>
      <div>
        <p class="text-white/40 text-xs uppercase tracking-wider">Return Date</p>
        <p class="text-white text-sm mt-0.5">{{ \Carbon\Carbon::parse($attRecord->return_date)->format('M d, Y') }}</p>
      </div>
      <div>
        <p class="text-white/40 text-xs uppercase tracking-wider">Filed At</p>
        <p class="text-white text-sm mt-0.5">{{ \Carbon\Carbon::parse($attRecord->filed_at)->format('M d, Y h:i A') }}</p>
      </div>
      @if($attRecord->approved_at)
      <div>
        <p class="text-white/40 text-xs uppercase tracking-wider">Reviewed At</p>
        <p class="text-white text-sm mt-0.5">{{ \Carbon\Carbon::parse($attRecord->approved_at)->format('M d, Y h:i A') }}</p>
      </div>
      @endif
    </div>
  </div>

  {{-- Uploaded Image --}}
  @if($attRecord->image_url)
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
      <p class="text-white/50 text-xs uppercase tracking-wider mb-3">Uploaded Form</p>
      <img src="{{ $attRecord->image_url }}" alt="ATT Form"
           class="w-full rounded-xl border border-white/10 max-h-96 object-contain bg-black/20"/>
    </div>
  @else
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
      <p class="text-white/30 text-sm text-center py-4">No image uploaded.</p>
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
                class="bg-[#4CAF82] hover:bg-[#3d9e71] text-white font-semibold
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
                class="bg-red-500/15 hover:bg-red-500/25 text-red-400 border border-red-500/20
                       font-semibold px-6 py-3 rounded-xl text-sm transition-colors">
          ✗ Reject
        </button>
      </form>
      <a href="{{ route('att-records.index') }}"
         class="text-white/40 hover:text-white text-sm transition-colors ml-auto">
        ← Back to List
      </a>
    </div>
  @else
    <a href="{{ route('att-records.index') }}"
       class="inline-block text-white/40 hover:text-white text-sm transition-colors">
      ← Back to List
    </a>
  @endif
</div>
@endsection
