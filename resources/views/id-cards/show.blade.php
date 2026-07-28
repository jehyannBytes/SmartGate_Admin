@extends('layouts.app')

@section('title', 'ID Card')
@section('page-title', 'Employee ID Card')
@section('page-subtitle', $employee->first_name . ' ' . $employee->last_name)

@section('content')
<div class="space-y-4">

    <a href="{{ route('employees.show', $employee) }}"
       class="text-white/40 hover:text-white text-xs font-medium transition-colors flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Employee Profile
    </a>

    <div class="flex flex-col items-center gap-6">

        {{-- Toggle buttons --}}
        <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl p-1">
            <button type="button" onclick="showSide('front')" id="btn-front"
                    class="px-5 py-1.5 rounded-lg text-sm font-medium transition-colors bg-[#4CAF82] text-white">
                Front
            </button>
            <button type="button" onclick="showSide('back')" id="btn-back"
                    class="px-5 py-1.5 rounded-lg text-sm font-medium transition-colors text-white/50 hover:text-white">
                Back
            </button>
        </div>

        {{-- FRONT CARD --}}
        <div id="card-front"
             class="bg-gradient-to-b from-[#0a1f18] to-[#04120c] border border-[#4CAF82]/30 rounded-3xl
                    w-[260px] h-[420px] p-5 flex flex-col shadow-2xl relative overflow-hidden">
<div class="flex items-center gap-2">
    @if(file_exists(public_path('images/logo.png')))
        <img src="{{ asset('images/logo.png') }}"
             class="w-10 h-10 object-contain flex-shrink-0"/>
    @endif

    <div class="text-left">
        <p class="text-white text-[9px] font-bold uppercase tracking-wide leading-tight">
            Pangasinan State University
        </p>
        <p class="text-white text-[9px] font-bold uppercase tracking-wide leading-tight">
            Alaminos City Campus
        </p>
        <p class="text-[#4CAF82] text-[8px] font-bold tracking-widest mt-1">
            SMARTGATE ACC
        </p>
    </div>
</div>
            <div class="w-[150px] h-[170px] mx-auto mt-3 mb-3 rounded-xl border-2 border-[#4CAF82]
                        bg-white/5 overflow-hidden flex items-center justify-center shrink-0">
                @if($employee->photo_url)
                    <img src="{{ asset($employee->photo_url) }}" alt="" class="w-full h-full object-cover"/>
                @else
                    <svg class="w-10 h-10 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                @endif
            </div>

            <p class="text-white text-center font-bold text-base leading-tight">{{ $employee->last_name }}, {{ $employee->first_name }}</p>
            <p class="text-white/70 text-center text-xs mt-1">{{ $employee->position }}</p>

            <div class="w-2/3 mx-auto border-t border-[#4CAF82]/40 my-2"></div>

            <p class="text-[#7fd6ab] text-center text-[10px] font-bold uppercase tracking-wide">{{ $employee->department }}</p>
            <p class="text-white text-center font-mono text-sm tracking-wider mt-1.5">{{ $employee->employee_code }}</p>

            <div class="absolute bottom-0 left-0 right-0 h-2 bg-[#4CAF82]"></div>
        </div>

        {{-- BACK CARD --}}
        <div id="card-back"
             class="hidden bg-gradient-to-b from-[#0a1f18] to-[#04120c] border border-[#4CAF82]/30 rounded-3xl
                    w-[260px] h-[420px] p-5 flex flex-col shadow-2xl relative overflow-hidden">
<div class="flex items-center gap-2">
    @if(file_exists(public_path('images/logo.png')))
        <img src="{{ asset('images/logo.png') }}"
             class="w-10 h-10 object-contain flex-shrink-0"/>
    @endif

    <div class="text-left">
        <p class="text-white text-[9px] font-bold uppercase tracking-wide leading-tight">
            Pangasinan State University
        </p>
        <p class="text-white text-[9px] font-bold uppercase tracking-wide leading-tight">
            Alaminos City Campus
        </p>
        <p class="text-[#4CAF82] text-[8px] font-bold tracking-widest mt-1">
            SMARTGATE ACC
        </p>
    </div>
</div>

            <p class="text-white/50 text-center text-[10px] uppercase tracking-widest mt-10">Scan for Attendance</p>

            <div class="w-[150px] h-[150px] mx-auto mt-4 rounded-xl bg-white p-2.5 flex items-center justify-center shrink-0">
                @if($qrSource)
                    <img src="{{ $qrSource }}" alt="QR Code" class="w-full h-full">
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center rounded">
                        <span class="text-[8px] text-gray-400 text-center px-2">No QR Code Generated</span>
                    </div>
                @endif
            </div>

            <p class="text-white/30 text-center text-[7px] mt-6 px-3 leading-relaxed">
                This ID is property of PSU-Alaminos City Campus. If found, please return to the ICT Management Office.
            </p>

            <div class="absolute bottom-0 left-0 right-0 h-2 bg-[#4CAF82]"></div>
        </div>

        @if(!$qrSource)
            <p class="text-amber-400 text-xs bg-amber-500/10 border border-amber-500/20 rounded-xl px-4 py-2">
                This employee has no active QR code yet. The ID card will print without a scannable code.
                <a href="{{ route('qr-codes.show', $employee) }}" class="underline">Generate one first</a>.
            </p>
        @endif

        <a href="{{ route('id-cards.download', $employee) }}"
           class="bg-[#4CAF82] hover:bg-[#3d9e71] text-white px-5 py-2.5 rounded-xl
                  text-sm font-semibold transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Download as PDF
        </a>
    </div>
</div>

<script>
    function showSide(side) {
        const front = document.getElementById('card-front');
        const back = document.getElementById('card-back');
        const btnFront = document.getElementById('btn-front');
        const btnBack = document.getElementById('btn-back');

        if (side === 'front') {
            front.classList.remove('hidden');
            back.classList.add('hidden');
            btnFront.classList.add('bg-[#4CAF82]', 'text-white');
            btnFront.classList.remove('text-white/50');
            btnBack.classList.remove('bg-[#4CAF82]', 'text-white');
            btnBack.classList.add('text-white/50');
        } else {
            back.classList.remove('hidden');
            front.classList.add('hidden');
            btnBack.classList.add('bg-[#4CAF82]', 'text-white');
            btnBack.classList.remove('text-white/50');
            btnFront.classList.remove('bg-[#4CAF82]', 'text-white');
            btnFront.classList.add('text-white/50');
        }
    }
</script>
@endsection
