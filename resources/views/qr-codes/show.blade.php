@extends('layouts.app')

@section('title', 'QR Code - ' . $employee->first_name . ' ' . $employee->last_name)
@section('page-title', $employee->first_name . ' ' . $employee->last_name)
@section('page-subtitle', 'QR code details and history')

@section('content')
<div class="space-y-4">

    <a href="{{ route('qr-codes.index') }}"
       class="text-white/40 hover:text-white text-xs font-medium transition-colors flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to QR Codes
    </a>

    @if(session('success'))
        <div class="bg-[#4CAF82]/10 border border-[#4CAF82]/20 text-[#4CAF82] text-sm rounded-xl px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-1 bg-white/5 border border-white/10 rounded-2xl p-6">
            <p class="text-white font-semibold">{{ $employee->last_name }}, {{ $employee->first_name }}</p>
            <p class="text-white/40 text-xs font-mono mt-0.5">{{ $employee->employee_code }}</p>
            <p class="text-white/40 text-xs mt-1">{{ $employee->position }} - {{ $employee->department }}</p>

            <div class="mt-5 pt-5 border-t border-white/5">
                @if($activeQr)
                    @if($qrImage)
                        <div class="bg-white rounded-xl p-4 flex items-center justify-center mb-4">
                            <img src="{{ $qrImage }}" alt="QR Code" class="w-40 h-40">
                        </div>
                    @endif

                    <p class="text-[#4CAF82] text-xs uppercase tracking-wider mb-2">Active QR Token</p>
                    <p class="text-white/70 text-xs font-mono break-all bg-white/5 rounded-lg p-3">
                        {{ $activeQr->qr_token }}
                    </p>
                    <p class="text-white/30 text-xs mt-2">
                        Issued {{ \Carbon\Carbon::parse($activeQr->issued_at)->diffForHumans() }}
                    </p>

                    <div class="flex flex-col gap-2 mt-4">
                        <form method="POST" action="{{ route('qr-codes.regenerate', $employee) }}"
                              onsubmit="return confirm('Regenerate QR code? The current code will stop working immediately.')">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-[#4CAF82] hover:bg-[#3d9e71] text-white text-sm font-semibold
                                           py-2.5 rounded-xl transition-colors">
                                Regenerate QR Code
                            </button>
                        </form>
                        <form method="POST" action="{{ route('qr-codes.destroy', $employee) }}"
                              onsubmit="return confirm('Revoke this QR code? The employee will not be able to use it until a new one is issued.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-white/5 hover:bg-red-500/10 border border-white/10 hover:border-red-500/20
                                           text-white/60 hover:text-red-400 text-sm font-medium py-2.5 rounded-xl transition-colors">
                                Revoke QR Code
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-white/30 text-sm mb-4">This employee has no active QR code.</p>
                    <form method="POST" action="{{ route('qr-codes.generate', $employee) }}">
                        @csrf
                        <button type="submit"
                                class="w-full bg-[#4CAF82] hover:bg-[#3d9e71] text-white text-sm font-semibold
                                       py-2.5 rounded-xl transition-colors">
                            Generate QR Code
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2 bg-white/5 border border-white/10 rounded-2xl p-6">
            <p class="text-white font-semibold text-sm mb-4">QR Code History</p>

            @if($history->isEmpty())
                <p class="text-white/30 text-sm text-center py-8">No QR codes have been issued yet.</p>
            @else
                <div class="space-y-2">
                    @foreach($history as $qr)
                    <div class="flex items-center justify-between gap-3 bg-white/5 rounded-xl px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-white/70 text-xs font-mono truncate">{{ $qr->qr_token }}</p>
                            <p class="text-white/30 text-xs mt-0.5">
                                Issued {{ \Carbon\Carbon::parse($qr->issued_at)->format('M d, Y g:i A') }}
                            </p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full shrink-0
                                    {{ $qr->is_active
                                        ? 'bg-[#4CAF82]/15 text-[#4CAF82] border border-[#4CAF82]/20'
                                        : 'bg-white/5 text-white/30 border border-white/10' }}">
                            {{ $qr->is_active ? 'Active' : 'Revoked' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection