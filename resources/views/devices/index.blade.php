@extends('layouts.app')

@section('title', 'Devices')
@section('page-title', 'Device Management')
@section('page-subtitle', 'Manage SmartGate scanner devices')

@section('content')
<div class="space-y-4">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <p class="text-white/50 text-xs uppercase tracking-wider mb-2">Total Devices</p>
            <p class="text-3xl font-bold text-white">{{ $total }}</p>
        </div>
        <div class="bg-[#4CAF82]/5 border border-[#4CAF82]/20 rounded-2xl p-5">
            <p class="text-[#4CAF82] text-xs uppercase tracking-wider mb-2">Online</p>
            <p class="text-3xl font-bold text-white">{{ $online }}</p>
            <p class="text-white/30 text-xs mt-1">Active in last 10 mins</p>
        </div>
        <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-5">
            <p class="text-red-400 text-xs uppercase tracking-wider mb-2">Offline</p>
            <p class="text-3xl font-bold text-white">{{ $offline }}</p>
            <p class="text-white/30 text-xs mt-1">No recent activity</p>
        </div>
    </div>

    {{-- Add Button --}}
    <div class="flex justify-end">
        <a href="{{ route('devices.create') }}"
           class="bg-[#4CAF82] hover:bg-[#3d9e71] text-white px-4 py-2.5
                  rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Register Device
        </a>
    </div>

    {{-- Device Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($devices as $device)
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5
                    {{ $device->is_online ? 'border-l-4 border-l-[#4CAF82]' : '' }}
                    hover:bg-white/8 transition-colors">
            <div class="flex items-start gap-4">

                {{-- Device Icon + Status --}}
                <div class="relative shrink-0">
                    <div class="w-12 h-12 rounded-xl
                                {{ $device->is_online ? 'bg-[#4CAF82]/15' : 'bg-white/5' }}
                                flex items-center justify-center">
                        <svg class="w-6 h-6 {{ $device->is_online ? 'text-[#4CAF82]' : 'text-white/30' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0
                                     002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75A2.25
                                     2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    {{-- Online/Offline dot --}}
                    <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2 border-[#0a0a0a]
                                {{ $device->is_online ? 'bg-[#4CAF82]' : ($device->is_active ? 'bg-red-500' : 'bg-white/20') }}">
                    </span>
                </div>

                {{-- Device Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $device->device_name }}</p>
                            <p class="text-white/40 text-xs font-mono mt-0.5">{{ $device->device_mac }}</p>
                        </div>
                        {{-- Type Badge --}}
                        <span class="text-xs px-2.5 py-1 rounded-full border shrink-0
                                    {{ $device->device_type === 'dedicated'
                                        ? 'bg-blue-500/15 text-blue-400 border-blue-500/20'
                                        : 'bg-purple-500/15 text-purple-400 border-purple-500/20' }}">
                            {{ ucfirst($device->device_type) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 mt-3">
                        {{-- Location --}}
                        <div class="flex items-center gap-1.5 text-white/40 text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                            {{ $device->location ?? 'No location set' }}
                        </div>

                        {{-- Last Seen --}}
                        <div class="flex items-center gap-1.5 text-xs
                                    {{ $device->is_online ? 'text-[#4CAF82]' : 'text-white/30' }}">
                            <span class="w-1.5 h-1.5 rounded-full
                                        {{ $device->is_online ? 'bg-[#4CAF82]' : 'bg-white/20' }}">
                            </span>
                            @if($device->last_seen_at)
                                {{ $device->is_online ? 'Online' : 'Last seen ' . \Carbon\Carbon::parse($device->last_seen_at)->diffForHumans() }}
                            @else
                                Never connected
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-white/5">
                <a href="{{ route('devices.show', $device) }}"
                   class="text-white/40 hover:text-[#4CAF82] text-xs font-medium transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36
                                 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431
                                 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638
                                 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    View Logs
                </a>
                <a href="{{ route('devices.edit', $device) }}"
                   class="text-white/40 hover:text-blue-400 text-xs font-medium transition-colors flex items-center gap-1.5 ml-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652
                                 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6
                                 18l.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487z"/>
                    </svg>
                    Edit
                </a>
                @if($device->is_active)
                    <form method="POST" action="{{ route('devices.destroy', $device) }}"
                          onsubmit="return confirm('Deactivate {{ $device->device_name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-white/30 hover:text-red-400 text-xs font-medium transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728
                                         12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Deactivate
                        </button>
                    </form>
                @else
                    <span class="ml-auto text-xs text-white/20">Inactive</span>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-2 bg-white/5 border border-white/10 rounded-2xl p-12 text-center">
            <p class="text-white/30 text-sm">No devices registered yet.</p>
            <a href="{{ route('devices.create') }}"
               class="inline-block mt-4 text-[#4CAF82] text-sm hover:underline">
                Register your first device &rarr;
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
