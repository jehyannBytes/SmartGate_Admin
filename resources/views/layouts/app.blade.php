{{-- resources/views/layouts/app.blade.php --}}
{{-- SmartGate ACC — Main Admin Layout (Sidebar + Topbar) --}}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'SmartGate ACC') — Admin</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-slate-900 min-h-screen flex">

 {{-- ── SIDEBAR ────────────────────────────────────────────────────── --}}
  <aside class="w-54 min-h-screen bg-gradient-to-b from-[#1e2a5e] to-[#141d47]
                flex flex-col fixed top-0 left-0 z-30 relative overflow-hidden">

    {{-- Decorative dot-grid pattern, bottom-left corner --}}
    <div class="pointer-events-none absolute bottom-0 left-0 w-40 h-56 opacity-40"
         style="background-image: radial-gradient(circle, #fbbf24 1px, transparent 1px);
                background-size: 12px 12px;
                mask-image: linear-gradient(to top right, black 20%, transparent 70%);
                -webkit-mask-image: linear-gradient(to top right, black 20%, transparent 70%);">
    </div>

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5">
      <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 bg-white/10 flex items-center justify-center">
        <img src="{{ asset('images/logo.png') }}" alt="SmartGate ACC logo" class="w-full h-full object-cover">
      </div>
      <div class="leading-tight">
        <p class="text-white font-bold text-base">
          SmartGate <span class="text-amber-400">ACC</span>
        </p>
        <p class="text-white/50 text-[10px] tracking-wider uppercase">Admin Dashboard</p>
      </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 pt-2 space-y-1.5 overflow-y-auto">

      {{-- Dashboard — both roles --}}
      <x-nav-item route="dashboard" icon="squares-2x2">
        Dashboard
      </x-nav-item>

      {{-- Attendance — both roles --}}
      <x-nav-item route="attendance.index" icon="clock">
        Attendance Logs
      </x-nav-item>

      {{-- Analytics — both roles --}}
      <x-nav-item route="analytics.index" icon="chart-bar">
        Analytics
      </x-nav-item>

      {{-- HR ONLY --}}
      @if(Auth::user()->isHR())
        <div class="pt-3 pb-1 px-3">
          <p class="text-[10px] text-white/30 uppercase tracking-widest">
            HR Management
          </p>
        </div>
        <x-nav-item route="att-records.index" icon="paper-airplane">
          ATT Records
        </x-nav-item>
        <x-nav-item route="locator-slips.index" icon="map-pin">
          Locator Slips
        </x-nav-item>
        <x-nav-item route="leave-requests.index" icon="calendar-days">
          Leave Requests
        </x-nav-item>
        <x-nav-item route="reports.index" icon="document-chart-bar">
          Monthly Reports
        </x-nav-item>
      @endif

      {{-- ICTMO ONLY --}}
      @if(Auth::user()->isICTMO())
        <div class="pt-3 pb-1 px-3">
          <p class="text-[10px] text-white/30 uppercase tracking-widest">
            System Management
          </p>
        </div>
        <x-nav-item route="employees.index" icon="users">
          Employees
        </x-nav-item>
        <x-nav-item route="devices.index" icon="device-tablet">
          Devices
        </x-nav-item>

        <x-nav-item route="notifications.index" icon="bell">
          Notifications
          @php
            $unread = Auth::user()->notifications()->where('is_read', false)->count();
          @endphp
          @if($unread > 0)
            <span class="ml-auto bg-red-500 text-white text-[10px]
                         font-bold px-1.5 py-0.5 rounded-full">
              {{ $unread }}
            </span>
          @endif
        </x-nav-item>

        <x-nav-item route="audit-logs.index" icon="clipboard-document-list">
          Audit Trail
        </x-nav-item>
      @endif
    </nav>

    {{-- Logout --}}
    <div class="p-4">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-3 rounded-xl
                       bg-white/5 text-white/70 hover:text-white hover:bg-white/10
                       transition-all duration-200 text-sm">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
               viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25
                     0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25
                     2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
          </svg>
          <span class="text-left leading-tight">
            <span class="block font-semibold">Log Out</span>
            <span class="block text-white/40 text-xs">Sign out from your account</span>
          </span>
        </button>
      </form>
    </div>
  </aside>
  {{-- ── MAIN CONTENT ───────────────────────────────────────────────── --}}
  <div class="flex-1 ml-54 flex flex-col min-h-screen">

    {{-- Topbar --}}
    <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md
               border-b border-slate-200 px-4 py-4 flex items-center gap-4">
      <div class="flex-1">
        <h1 class="text-slate-900 font-semibold text-lg">
          @yield('page-title', 'Dashboard')
        </h1>
        <p class="text-slate-400 text-xs mt-0.5">
          @yield('page-subtitle', '')
        </p>
      </div>

      {{-- Current date --}}
      <div class="text-right hidden sm:block">
        <p class="text-slate-600 text-sm font-medium" id="topbar-time">--:-- --</p>
        <p class="text-slate-400 text-xs" id="topbar-date">Loading...</p>
      </div>
    </header>

    {{-- Page Content --}}
    <main class="flex-1 p-2">
      {{-- Flash Messages --}}
      @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 rounded-xl
                    bg-[#4CAF82]/10 border border-[#4CAF82]/30 text-[#4CAF82]">
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
               viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
      @endif

      @if(session('error'))
        <div class="mb-6 flex items-center gap-3 p-4 rounded-xl
                    bg-red-500/10 border border-red-500/30 text-red-400">
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
               viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9
                     3.75h.008v.008H12v-.008z"/>
          </svg>
          <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  {{-- Live clock script --}}
  <script>
    function updateClock() {
      const now = new Date();
      const h = now.getHours() % 12 || 12;
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      const period = now.getHours() >= 12 ? 'PM' : 'AM';
      const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
      const months = ['Jan','Feb','Mar','Apr','May','Jun',
                      'Jul','Aug','Sep','Oct','Nov','Dec'];
      document.getElementById('topbar-time').textContent = `${h}:${m}:${s} ${period}`;
      document.getElementById('topbar-date').textContent =
        `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
    }
    updateClock();
    setInterval(updateClock, 1000);
  </script>

</body>
</html>
