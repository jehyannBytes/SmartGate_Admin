@extends('layouts.app')
@section('title', $employee->full_name)
@section('page-title', $employee->full_name)
@section('page-subtitle', $employee->position . ' · ' . $employee->department)

@section('content')
<div class="space-y-6">

  {{-- Profile Header --}}
  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
    <div class="flex items-start gap-6">
      {{-- Avatar --}}
      <div class="w-20 h-20 rounded-2xl bg-emerald-50 border-2 border-emerald-200
                  flex items-center justify-center shrink-0">
        @if($employee->photo_url)
          <img src="{{ asset($employee->photo_url) }}" alt="{{ $employee->full_name }}"
               class="w-full h-full rounded-2xl object-cover"/>
        @else
          <span class="text-emerald-600 text-2xl font-bold">
            {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
          </span>
        @endif
      </div>

      {{-- Info --}}
      <div class="flex-1">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-slate-900 text-2xl font-bold">{{ $employee->full_name }}</h2>
            <p class="text-slate-500 text-sm mt-1">{{ $employee->position }}</p>
            <p class="text-slate-400 text-sm">{{ $employee->department }}</p>
          </div>
          <div class="flex items-center gap-2">
            <a href="{{ route('id-cards.show', $employee) }}"
                       class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-sm
                              font-medium px-4 py-2 rounded-xl transition-colors">
                        View ID Card
                    </a>
                    <a href="{{ route('employees.edit', $employee) }}"
               class="bg-blue-50 text-blue-600 border border-blue-200 px-4 py-2
                      rounded-xl text-sm font-semibold hover:bg-blue-100 transition-colors">
              Edit
            </a>
            <a href="{{ route('employees.index') }}"
               class="bg-white text-slate-500 border border-slate-200 px-4 py-2
                      rounded-xl text-sm hover:bg-slate-50 transition-colors">
              Back
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Employee Details --}}
    <div class="lg:col-span-1 bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
      <h3 class="text-slate-900 font-semibold mb-4">Employee Details</h3>
      <div class="space-y-3">
        <div>
          <p class="text-slate-400 text-xs uppercase tracking-wider">Employee Code</p>
          <p class="text-slate-900 font-mono text-sm mt-0.5">{{ $employee->employee_code }}</p>
        </div>
        <div>
          <p class="text-slate-400 text-xs uppercase tracking-wider">Full Name</p>
          <p class="text-slate-900 text-sm mt-0.5">{{ $employee->full_name }}</p>
        </div>
        <div>
          <p class="text-slate-400 text-xs uppercase tracking-wider">Department</p>
          <p class="text-slate-900 text-sm mt-0.5">{{ $employee->department }}</p>
        </div>
        <div>
          <p class="text-slate-400 text-xs uppercase tracking-wider">Position</p>
          <p class="text-slate-900 text-sm mt-0.5">{{ $employee->position }}</p>
        </div>
        <div>
          <p class="text-slate-400 text-xs uppercase tracking-wider">Employment Type</p>
          <span class="inline-block mt-0.5 text-xs font-semibold px-2.5 py-1 rounded-full
                       {{ $employee->employment_type === 'faculty'
                          ? 'bg-blue-50 text-blue-600 border border-blue-200'
                          : 'bg-purple-50 text-purple-600 border border-purple-200' }}">
            {{ ucfirst($employee->employment_type) }}
          </span>
        </div>
        <div>
          <p class="text-slate-400 text-xs uppercase tracking-wider">Status</p>
          <span class="inline-flex items-center gap-1.5 mt-0.5 text-xs font-semibold
                       {{ $employee->is_active ? 'text-emerald-600' : 'text-red-500' }}">
            <span class="w-1.5 h-1.5 rounded-full
                         {{ $employee->is_active ? 'bg-emerald-500' : 'bg-red-500' }}">
            </span>
            {{ $employee->is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
        <div>
          <p class="text-slate-400 text-xs uppercase tracking-wider">Date Added</p>
          <p class="text-slate-900 text-sm mt-0.5">
            {{ $employee->created_at?->format('M d, Y') ?? 'N/A' }}
          </p>
        </div>
      </div>
    </div>

    {{-- Recent Attendance Logs --}}
    <div class="lg:col-span-2 bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
      <h3 class="text-slate-900 font-semibold mb-4">Recent Attendance Logs</h3>

      @if($employee->attendanceLogs->isEmpty())
        <p class="text-slate-400 text-sm text-center py-8">No attendance logs yet.</p>
      @else
        <div class="space-y-2">
          @foreach($employee->attendanceLogs as $log)
            @php
              $colors = [
                'Morning In'    => 'bg-blue-50 text-blue-600 border-blue-200',
                'Morning Out'   => 'bg-sky-50 text-sky-600 border-sky-200',
                'Afternoon In'  => 'bg-amber-50 text-amber-600 border-amber-200',
                'Afternoon Out' => 'bg-orange-50 text-orange-600 border-orange-200',
              ];
              $color = $colors[$log->log_type] ?? 'bg-slate-100 text-slate-500 border-slate-200';
            @endphp
            <div class="flex items-center gap-4 px-4 py-3 rounded-xl bg-slate-50">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $color }}">
                {{ $log->log_type }}
              </span>
              <span class="text-slate-900 text-sm">
                {{ \Carbon\Carbon::parse($log->scanned_at)->format('M d, Y h:i A') }}
              </span>
              <span class="ml-auto">
                @if($log->face_verified)
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                       viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                @else
                  <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor"
                       viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                @endif
              </span>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection