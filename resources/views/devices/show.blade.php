@extends('layouts.app')

@section('title', $device->device_name)
@section('page-title', $device->device_name)
@section('page-subtitle', 'Device details and recent scan activity')

@section('content')
<div class="space-y-4">

    {{-- Back link --}}
    <a href="{{ route('devices.index') }}"
       class="text-slate-400 hover:text-slate-900 text-xs font-medium transition-colors flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Devices
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Device Info Card --}}
        <div class="lg:col-span-1 bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start gap-4 mb-5">
                <div class="relative shrink-0">
                    <div class="w-14 h-14 rounded-xl
                                {{ $device->is_online ? 'bg-emerald-50' : 'bg-slate-100' }}
                                flex items-center justify-center">
                        <svg class="w-7 h-7 {{ $device->is_online ? 'text-emerald-600' : 'text-slate-400' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0
                                     002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75A2.25
                                     2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white
                                {{ $device->is_online ? 'bg-emerald-500' : ($device->is_active ? 'bg-red-500' : 'bg-slate-300') }}">
                    </span>
                </div>
                <div>
                    <p class="text-slate-900 font-semibold">{{ $device->device_name }}</p>
                    <p class="text-slate-400 text-xs font-mono mt-0.5">{{ $device->device_mac }}</p>
                </div>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-400">Type</span>
                    <span class="text-xs px-2.5 py-1 rounded-full border
                                {{ $device->device_type === 'dedicated'
                                    ? 'bg-blue-50 text-blue-600 border-blue-200'
                                    : 'bg-purple-50 text-purple-600 border-purple-200' }}">
                        {{ ucfirst($device->device_type) }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-400">Location</span>
                    <span class="text-slate-900">{{ $device->location ?? 'Not set' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-400">Status</span>
                    <span class="{{ $device->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ $device->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-slate-400">Last Seen</span>
                    <span class="{{ $device->is_online ? 'text-emerald-600' : 'text-slate-900' }}">
                        @if($device->last_seen_at)
                            {{ $device->is_online ? 'Online now' : \Carbon\Carbon::parse($device->last_seen_at)->diffForHumans() }}
                        @else
                            Never connected
                        @endif
                    </span>
                </div>
            </div>

            <a href="{{ route('devices.edit', $device) }}"
               class="mt-5 block text-center bg-white hover:bg-slate-50 border border-slate-200
                      text-slate-700 text-sm font-medium py-2.5 rounded-xl transition-colors">
                Edit Device
            </a>
        </div>

        {{-- Recent Logs --}}
        <div class="lg:col-span-2 bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
            <p class="text-slate-900 font-semibold text-sm mb-4">Recent Scan Activity</p>

            @if($recentLogs->isEmpty())
                <p class="text-slate-400 text-sm text-center py-8">No scan activity recorded yet.</p>
            @else
                <div class="space-y-2">
                    @foreach($recentLogs as $log)
                    <div class="flex items-center justify-between gap-3 bg-slate-50 rounded-xl px-4 py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-slate-900 text-sm truncate">{{ $log->employee->name ?? 'Unknown employee' }}</p>
                                <p class="text-slate-400 text-xs">{{ \Carbon\Carbon::parse($log->scanned_at)->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        <span class="text-slate-400 text-xs shrink-0">
                            {{ \Carbon\Carbon::parse($log->scanned_at)->diffForHumans() }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection