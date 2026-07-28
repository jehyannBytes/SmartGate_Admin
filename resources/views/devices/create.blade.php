@extends('layouts.app')

@section('title', 'Register Device')
@section('page-title', 'Register Device')
@section('page-subtitle', 'Add a new SmartGate scanner device')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <form method="POST" action="{{ route('devices.store') }}" class="space-y-5">
            @csrf

            {{-- Device Name --}}
            <div>
                <label class="block text-white/50 text-xs uppercase tracking-wider mb-2">
                    Device Name <span class="text-red-400">*</span>
                </label>
                <input type="text" name="device_name" value="{{ old('device_name') }}"
                       placeholder="e.g. Main Gate Scanner"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                              text-white text-sm placeholder-white/20 focus:outline-none
                              focus:border-[#4CAF82]/50 focus:ring-1 focus:ring-[#4CAF82]/50
                              @error('device_name') border-red-500/50 @enderror">
                @error('device_name')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Device MAC --}}
            <div>
                <label class="block text-white/50 text-xs uppercase tracking-wider mb-2">
                    Device MAC Address <span class="text-red-400">*</span>
                </label>
                <input type="text" name="device_mac" value="{{ old('device_mac') }}"
                       placeholder="e.g. 00:1A:2B:3C:4D:5E"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                              text-white text-sm font-mono placeholder-white/20 focus:outline-none
                              focus:border-[#4CAF82]/50 focus:ring-1 focus:ring-[#4CAF82]/50
                              @error('device_mac') border-red-500/50 @enderror">
                @error('device_mac')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Device Type --}}
            <div>
                <label class="block text-white/50 text-xs uppercase tracking-wider mb-2">
                    Device Type <span class="text-red-400">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-2 bg-white/5 border border-white/10
                                  rounded-xl px-4 py-3 cursor-pointer hover:bg-white/8 transition-colors
                                  has-[:checked]:border-blue-500/50 has-[:checked]:bg-blue-500/10">
                        <input type="radio" name="device_type" value="dedicated"
                               {{ old('device_type') == 'dedicated' ? 'checked' : '' }}
                               class="accent-blue-500">
                        <span class="text-white text-sm">Dedicated</span>
                    </label>
                    <label class="relative flex items-center gap-2 bg-white/5 border border-white/10
                                  rounded-xl px-4 py-3 cursor-pointer hover:bg-white/8 transition-colors
                                  has-[:checked]:border-purple-500/50 has-[:checked]:bg-purple-500/10">
                        <input type="radio" name="device_type" value="mobile"
                               {{ old('device_type') == 'mobile' ? 'checked' : '' }}
                               class="accent-purple-500">
                        <span class="text-white text-sm">Mobile</span>
                    </label>
                </div>
                @error('device_type')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Location --}}
            <div>
                <label class="block text-white/50 text-xs uppercase tracking-wider mb-2">
                    Location
                </label>
                <input type="text" name="location" value="{{ old('location') }}"
                       placeholder="e.g. Main Gate, Building A"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                              text-white text-sm placeholder-white/20 focus:outline-none
                              focus:border-[#4CAF82]/50 focus:ring-1 focus:ring-[#4CAF82]/50
                              @error('location') border-red-500/50 @enderror">
                @error('location')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-white/5">
                <button type="submit"
                        class="bg-[#4CAF82] hover:bg-[#3d9e71] text-white px-5 py-2.5
                               rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Register Device
                </button>
                <a href="{{ route('devices.index') }}"
                   class="text-white/40 hover:text-white text-sm font-medium transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
