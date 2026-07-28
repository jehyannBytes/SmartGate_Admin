@extends('layouts.app')

@section('title', 'QR Codes')
@section('page-title', 'QR Code Management')
@section('page-subtitle', 'Issue and manage employee QR codes')

@section('content')
<div class="space-y-4">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <p class="text-white/50 text-xs uppercase tracking-wider mb-2">Total Employees</p>
            <p class="text-3xl font-bold text-white">{{ $totalEmployees }}</p>
        </div>
        <div class="bg-[#4CAF82]/5 border border-[#4CAF82]/20 rounded-2xl p-5">
            <p class="text-[#4CAF82] text-xs uppercase tracking-wider mb-2">QR Issued</p>
            <p class="text-3xl font-bold text-white">{{ $issuedCount }}</p>
        </div>
        <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-5">
            <p class="text-red-400 text-xs uppercase tracking-wider mb-2">No QR Code</p>
            <p class="text-3xl font-bold text-white">{{ $noQrCount }}</p>
        </div>
    </div>

    {{-- Search + Filter --}}
    <form method="GET" action="{{ route('qr-codes.index') }}" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name or employee code..."
               class="flex-1 min-w-[200px] bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                      text-white text-sm placeholder-white/20 focus:outline-none
                      focus:border-[#4CAF82]/50 focus:ring-1 focus:ring-[#4CAF82]/50">

        <select name="qr_status" onchange="this.form.submit()"
                class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                       text-white text-sm focus:outline-none focus:border-[#4CAF82]/50">
            <option value="">All Statuses</option>
            <option value="issued" {{ request('qr_status') == 'issued' ? 'selected' : '' }}>QR Issued</option>
            <option value="none" {{ request('qr_status') == 'none' ? 'selected' : '' }}>No QR Code</option>
        </select>

        <button type="submit"
                class="bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm
                       font-medium px-4 py-2.5 rounded-xl transition-colors">
            Search
        </button>
    </form>

    {{-- Employee List --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-white/40 text-xs uppercase tracking-wider">
                    <th class="text-left px-5 py-3">Employee</th>
                    <th class="text-left px-5 py-3">Code</th>
                    <th class="text-left px-5 py-3">Department</th>
                    <th class="text-left px-5 py-3">QR Status</th>
                    <th class="text-left px-5 py-3">Issued</th>
                    <th class="text-right px-5 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                @php $qr = $employee->qrCodes->first(); @endphp
                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                    <td class="px-5 py-3">
                        <p class="text-white font-medium">{{ $employee->last_name }}, {{ $employee->first_name }}</p>
                        <p class="text-white/40 text-xs">{{ $employee->position }}</p>
                    </td>
                    <td class="px-5 py-3 text-white/60 font-mono text-xs">{{ $employee->employee_code }}</td>
                    <td class="px-5 py-3 text-white/60">{{ $employee->department }}</td>
                    <td class="px-5 py-3">
                        @if($qr && $qr->is_active)
                            <span class="text-xs px-2.5 py-1 rounded-full bg-[#4CAF82]/15 text-[#4CAF82] border border-[#4CAF82]/20">
                                Issued
                            </span>
                        @else
                            <span class="text-xs px-2.5 py-1 rounded-full bg-white/5 text-white/30 border border-white/10">
                                No QR Code
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-white/40 text-xs">
                        {{ $qr ? \Carbon\Carbon::parse($qr->issued_at)->diffForHumans() : '—' }}
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('qr-codes.show', $employee) }}"
                           class="text-[#4CAF82] hover:underline text-xs font-medium">
                            Manage
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-white/30 text-sm py-10">No employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $employees->links() }}
</div>
@endsection
