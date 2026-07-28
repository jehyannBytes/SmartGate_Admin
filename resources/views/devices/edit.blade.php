@extends('layouts.app')

@section('title', 'Edit Device')
@section('page-title', 'Edit Device')
@section('page-subtitle', 'Update SmartGate scanner device details')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <form method="POST" action="{{ route('devices.update', $device) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Device Name --}}
            <div>
                <label class="block text-white/50 text-xs uppercase tracking-wider mb-2">
                    Device Name <span class="text-red-400">*</span>
                </label>
                <input type="text" name="device_name" value="{{ old('device_name', $device->device_name) }}"
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
                <input type="text" name="device_mac" value="{{ old('device_mac', $device->device_mac) }}"
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
                               {{ old('device_type', $device->device_type) == 'dedicated' ? 'checked' : '' }}
                               class="accent-blue-500">
                        <span class="text-white text-sm">Dedicated</span>
                    </label>
                    <label class="relative flex items-center gap-2 bg-white/5 border border-white/10
                                  rounded-xl px-4 py-3 cursor-pointer hover:bg-white/8 transition-colors
                                  has-[:checked]:border-purple-500/50 has-[:checked]:bg-purple-500/10">
                        <input type="radio" name="device_type" value="mobile"
                               {{ old('device_type', $device->device_type) == 'mobile' ? 'checked' : '' }}
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
                <input type="text" name="location" value="{{ old('location', $device->location) }}"
                       placeholder="e.g. Main Gate, Building A"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                              text-white text-sm placeholder-white/20 focus:outline-none
                              focus:border-[#4CAF82]/50 focus:ring-1 focus:ring-[#4CAF82]/50
                              @error('location') border-red-500/50 @enderror">
                @error('location')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-white/50 text-xs uppercase tracking-wider mb-2">
                    Status
                </label>
                <label class="relative flex items-center gap-3 bg-white/5 border border-white/10
                              rounded-xl px-4 py-3 cursor-pointer hover:bg-white/8 transition-colors">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $device->is_active) ? 'checked' : '' }}
                           class="accent-[#4CAF82] w-4 h-4">
                    <span class="text-white text-sm">Device is active</span>
                </label>
                @error('is_active')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Last Seen (read-only info) --}}
            <div class="bg-white/5 border border-white/5 rounded-xl px-4 py-3">
                <p class="text-white/30 text-xs uppercase tracking-wider mb-1">Last Seen</p>
                <p class="text-white/60 text-sm">
                    @if($device->last_seen_at)
                        {{ $device->is_online ? 'Online now' : \Carbon\Carbon::parse($device->last_seen_at)->diffForHumans() }}
                    @else
                        Never connected
                    @endif
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-white/5">
                <button type="submit"
                        class="bg-[#4CAF82] hover:bg-[#3d9e71] text-white px-5 py-2.5
                               rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Update Device
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
