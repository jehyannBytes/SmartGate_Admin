@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'System Notifications')
@section('page-subtitle', 'System alerts and error notifications')

@section('content')
<div class="space-y-4">

    @if($notifications->count() > 0)
        <div class="flex justify-end">
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-sm
                               font-medium px-4 py-2 rounded-xl transition-colors">
                    Mark all as read
                </button>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($notifications as $notif)
        <div class="bg-white border rounded-2xl p-5 flex flex-col justify-between shadow-sm
                    {{ $notif->is_read ? 'border-[#1e2a5e]/30' : 'border-emerald-300' }}">

            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-2 h-2 rounded-full shrink-0
                                {{ $notif->is_read ? 'bg-slate-300' : 'bg-emerald-500' }}"></span>

                    @if(!$notif->is_read)
                        <span class="text-[10px] uppercase tracking-wide font-semibold text-emerald-600 bg-emerald-50
                                     px-2 py-0.5 rounded-full">
                            New
                        </span>
                    @endif
                </div>

                <p class="text-slate-900 text-sm leading-relaxed">{{ $notif->message }}</p>
            </div>

            <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                <p class="text-slate-400 text-xs">{{ $notif->created_at->diffForHumans() }}</p>

                @if(!$notif->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notif) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-slate-400 hover:text-emerald-600 text-xs font-medium transition-colors">
                            Mark as read
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
            <p class="text-slate-400 text-sm text-center py-8">No notifications yet.</p>
        </div>
        @endforelse
    </div>

    {{ $notifications->links() }}
</div>
@endsection