@extends('layouts.app')
@section('title', 'File Authority to Travel')
@section('page-title', 'Pangasinan State University — Authority to Travel')
@section('page-subtitle', 'Alaminos City Campus · Form FM-AD-HRD-09')

@section('content')
<div class="max-w-3xl bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6 mx-auto">
  
  <div class="border-b pb-4 text-center">
    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">Authority to Travel</h2>
    <p class="text-xs text-slate-500">PANGASINAN STATE UNIVERSITY — ALAMINOS CITY CAMPUS</p>
  </div>

  <form method="POST" action="{{ route('att-records.store') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf

    {{-- Header Info --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Date of Filing</label>
        <input type="date" name="filed_at" value="{{ date('Y-m-d') }}" required class="w-full border rounded-xl px-3 py-2 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">ATT No.</label>
        <input type="text" name="att_number" placeholder="2026-XXXX" class="w-full border rounded-xl px-3 py-2 text-sm">
      </div>
    </div>

    {{-- Employee Selection --}}
    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Employee Name</label>
      <select name="employee_id" required class="w-full border rounded-xl px-3 py-2 text-sm">
        <option value="">-- Select Requesting Employee --</option>
        @foreach($employees as $emp)
          <option value="{{ $emp->employee_id }}">{{ $emp->last_name }}, {{ $emp->first_name }} — {{ $emp->position }} ({{ $emp->department }})</option>
        @endforeach
      </select>
    </div>

    {{-- Travel Details --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Destination</label>
        <input type="text" name="destination" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="Location/Venue">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Travel Classification</label>
        <select name="travel_type" class="w-full border rounded-xl px-3 py-2 text-sm">
          <option value="Official Business">Official Business</option>
          <option value="Official Time">Official Time</option>
        </select>
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Purpose of Travel</label>
      <textarea name="purpose" rows="2" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="Purpose, activity, or project..."></textarea>
    </div>

    {{-- Schedule Grid --}}
    <div class="border rounded-xl p-4 bg-slate-50 space-y-3">
      <p class="text-xs font-bold text-slate-700 uppercase">Travel Schedule</p>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-[11px] text-slate-500">Departure Date & Time</label>
          <div class="flex gap-2 mt-1">
            <input type="date" name="departure_date" required class="w-full border rounded-lg px-2 py-1.5 text-xs">
            <input type="time" name="departure_time" class="w-28 border rounded-lg px-2 py-1.5 text-xs">
          </div>
        </div>
        <div>
          <label class="block text-[11px] text-slate-500">Arrival Date & Time</label>
          <div class="flex gap-2 mt-1">
            <input type="date" name="arrival_date" required class="w-full border rounded-lg px-2 py-1.5 text-xs">
            <input type="time" name="arrival_time" class="w-28 border rounded-lg px-2 py-1.5 text-xs">
          </div>
        </div>
      </div>
    </div>

    {{-- Signatories & Attachment --}}
    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Supervisor / Division Head Name</label>
      <input type="text" name="supervisor_name" placeholder="Name of Immediate Supervisor" class="w-full border rounded-xl px-3 py-2 text-sm">
    </div>

    <div>
      <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Attach Scanned Physical ATT Form</label>
      <input type="file" name="attachment" accept="image/*,.pdf" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-[#1e2a5e] file:text-white">
    </div>

    <div class="flex items-center justify-end gap-3 pt-4 border-t">
      <a href="{{ route('att-records.index') }}" class="text-slate-500 text-sm">Cancel</a>
      <button type="submit" class="bg-[#1e2a5e] text-white px-6 py-2.5 rounded-xl text-sm font-semibold">Save & Integrate with DTR</button>
    </div>
  </form>
</div>
@endsection