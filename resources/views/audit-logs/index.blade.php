@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')
@section('page-subtitle', 'Record of admin actions across the system')

@section('content')
<div class="space-y-4">

    <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search description or subject..."
               class="flex-1 min-w-[200px] bg-white border border-slate-200 rounded-xl px-4 py-2.5
                      text-slate-900 text-sm placeholder-slate-300 focus:outline-none
                      focus:border-[#1e2a5e]/50 focus:ring-1 focus:ring-[#1e2a5e]/50">

        <select name="admin_id" onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-xl px-4 py-2.5
                       text-slate-700 text-sm focus:outline-none focus:border-[#1e2a5e]/50">
            <option value="">All Admins</option>
            @foreach($admins as $admin)
                <option value="{{ $admin->admin_id }}" {{ request('admin_id') == $admin->admin_id ? 'selected' : '' }}>
                    {{ $admin->full_name }} ({{ $admin->username }})
                </option>
            @endforeach
        </select>

        <select name="action" onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-xl px-4 py-2.5
                       text-slate-700 text-sm focus:outline-none focus:border-[#1e2a5e]/50">
            <option value="">All Actions</option>
            @foreach($actionTypes as $type)
                <option value="{{ $type }}" {{ request('action') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
            @endforeach
        </select>

        <select name="subject_type" onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-xl px-4 py-2.5
                       text-slate-700 text-sm focus:outline-none focus:border-[#1e2a5e]/50">
            <option value="">All Types</option>
            @foreach($subjectTypes as $type)
                <option value="{{ $type }}" {{ request('subject_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-sm
                       font-medium px-4 py-2.5 rounded-xl transition-colors">
            Filter
        </button>
    </form>

    <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-slate-400 text-xs uppercase tracking-wider">
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
                <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3 text-slate-600 text-xs whitespace-nowrap">
                        {{ $log->created_at->format('M d, Y g:i A') }}
                    </td>
                    <td class="px-5 py-3 text-slate-900 text-xs">
                        {{ $log->admin->full_name ?? 'System' }}
                    </td>
                    <td class="px-5 py-3">
                        @php
                            $badge = match($log->action) {
                                'created', 'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                'deleted', 'rejected', 'deactivated', 'revoked' => 'bg-red-50 text-red-600 border-red-200',
                                'updated', 'regenerated' => 'bg-blue-50 text-blue-600 border-blue-200',
                                default => 'bg-slate-100 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full border {{ $badge }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-600 text-xs">
                        {{ $log->subject_type }}
                        @if($log->subject_label)
                            <span class="text-slate-900">- {{ $log->subject_label }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-slate-500 text-xs max-w-xs truncate">
                        {{ $log->description ?? '-' }}
                    </td>
                    <td class="px-5 py-3 text-slate-400 text-xs font-mono">
                        {{ $log->ip_address ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-slate-400 text-sm py-10">No audit logs recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
@endsection