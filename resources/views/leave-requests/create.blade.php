@extends('layouts.app')
@section('title', 'File Application for Leave')
@section('page-title', 'File Application for Leave')
@section('page-subtitle', 'Attach CS Form No. 6 scan and enter record details')

@section('content')
<div class="max-w-4xl mx-auto">
  <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm space-y-6">
    @csrf

    <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
      <div>
        <h3 class="text-base font-bold text-slate-900">CS Form No. 6 Details</h3>
        <p class="text-xs text-slate-500">Record physical leave application details for automated DTR sync.</p>
      </div>
      <a href="{{ route('leave-requests.index') }}" class="text-xs text-slate-500 hover:text-slate-900">← Back to List</a>
    </div>

    {{-- Employee & Filing Date --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Employee <span class="text-red-500">*</span></label>
        <select name="employee_id" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40" required>
          <option value="">Select Employee</option>
          @foreach($employees as $emp)
            <option value="{{ $emp->employee_id }}">{{ $emp->last_name }}, {{ $emp->first_name }} ({{ $emp->position }})</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Date of Filing <span class="text-red-500">*</span></label>
        <input type="date" name="filed_at" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40" required>
      </div>
    </div>

    {{-- Type of Leave & Details --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">6.A Type of Leave <span class="text-red-500">*</span></label>
        <select name="leave_type" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40" required>
          <option value="Vacation Leave">Vacation Leave</option>
          <option value="Mandatory/Forced Leave">Mandatory / Forced Leave</option>
          <option value="Sick Leave">Sick Leave</option>
          <option value="Maternity Leave">Maternity Leave</option>
          <option value="Paternity Leave">Paternity Leave</option>
          <option value="Special Privilege Leave">Special Privilege Leave</option>
          <option value="Solo Parent Leave">Solo Parent Leave</option>
          <option value="Study Leave">Study Leave</option>
          <option value="10-Day VAWC Leave">10-Day VAWC Leave</option>
          <option value="Rehabilitation Privilege">Rehabilitation Privilege</option>
          <option value="Special Emergency Leave">Special Emergency Leave</option>
          <option value="Adoption Leave">Adoption Leave</option>
          <option value="Others">Others</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">6.B Details / Location (Optional)</label>
        <input type="text" name="details_location" placeholder="e.g. Within Philippines, Out Patient, Illness" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40">
      </div>
    </div>

    {{-- Working Days & Inclusive Dates --}}
    <div class="grid grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Number of Days <span class="text-red-500">*</span></label>
        <input type="number" step="0.5" name="total_days" placeholder="e.g. 1" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Start Date <span class="text-red-500">*</span></label>
        <input type="date" name="start_date" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">End Date <span class="text-red-500">*</span></label>
        <input type="date" name="end_date" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40" required>
      </div>
    </div>

    {{-- Scan Attachment --}}
    <div>
      <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Scanned Application Form (CS Form No. 6) <span class="text-red-500">*</span></label>
      <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-900 focus:outline-none" required>
      <p class="text-xs text-slate-400 mt-1">Accepted formats: PDF, JPG, PNG (Max 5MB)</p>
    </div>

    {{-- Submit Actions --}}
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
      <a href="{{ route('leave-requests.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">Cancel</a>
      <button type="submit" class="bg-[#1e2a5e] hover:bg-[#1e2a5e]/90 text-white font-semibold px-5 py-2 rounded-xl text-xs transition-colors shadow-sm">
        Save & Integrate Leave
      </button>
    </div>
  </form>
</div>
@endsection