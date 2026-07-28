@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')
@section('page-subtitle', 'Record of admin actions across the system')

@section('content')
<div class="space-y-4">

    <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search description or subject..."
               class="flex-1 min-w-[200px] bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                      text-white text-sm placeholder-white/20 focus:outline-none
                      focus:border-[#4CAF82]/50 focus:ring-1 focus:ring-[#4CAF82]/50">

        <select name="admin_id" onchange="this.form.submit()"
                class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                       text-white text-sm focus:outline-none focus:border-[#4CAF82]/50">
            <option value="">All Admins</option>
            @foreach($admins as $admin)
                <option value="{{ $admin->admin_id }}" {{ request('admin_id') == $admin->admin_id ? 'selected' : '' }}>
                    {{ $admin->full_name }} ({{ $admin->username }})
                </option>
            @endforeach
        </select>

        <select name="action" onchange="this.form.submit()"
                class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                       text-white text-sm focus:outline-none focus:border-[#4CAF82]/50">
            <option value="">All Actions</option>
            @foreach($actionTypes as $type)
                <option value="{{ $type }}" {{ request('action') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
            @endforeach
        </select>

        <select name="subject_type" onchange="this.form.submit()"
                class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                       text-white text-sm focus:outline-none focus:border-[#4CAF82]/50">
            <option value="">All Types</option>
            @foreach($subjectTypes as $type)
                <option value="{{ $type }}" {{ request('subject_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm
                       font-medium px-4 py-2.5 rounded-xl transition-colors">
            Filter
        </button>
    </form>

    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-white/40 text-xs uppercase tracking-wider">
                    <th class="text-left px-5 py-3">Date/Time</th>
                    <th class="text-left px-5 py-3">Admin</th>
                    <th class="text-left px-5 py-3">Action</th>
                    <th class="text-left px-5 py-3">Subject</th>
                    <th class="text-left px-5 py-3">Description</th>
                    <th class="text-left px-5 py-3">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                    <td class="px-5 py-3 text-white/60 text-xs whitespace-nowrap">
                        {{ $log->created_at->format('M d, Y g:i A') }}
                    </td>
                    <td class="px-5 py-3 text-white/80 text-xs">
                        {{ $log->admin->full_name ?? 'System' }}
                    </td>
                    <td class="px-5 py-3">
                        @php
                            $badge = match($log->action) {
                                'created', 'approved' => 'bg-[#4CAF82]/15 text-[#4CAF82] border-[#4CAF82]/20',
                                'deleted', 'rejected', 'deactivated', 'revoked' => 'bg-red-500/15 text-red-400 border-red-500/20',
                                'updated', 'regenerated' => 'bg-blue-500/15 text-blue-400 border-blue-500/20',
                                default => 'bg-white/5 text-white/40 border-white/10',
                            };
                        @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full border {{ $badge }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-white/60 text-xs">
                        {{ $log->subject_type }}
                        @if($log->subject_label)
                            <span class="text-white/80">- {{ $log->subject_label }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-white/50 text-xs max-w-xs truncate">
                        {{ $log->description ?? '-' }}
                    </td>
                    <td class="px-5 py-3 text-white/30 text-xs font-mono">
                        {{ $log->ip_address ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-white/30 text-sm py-10">No audit logs recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
@endsection