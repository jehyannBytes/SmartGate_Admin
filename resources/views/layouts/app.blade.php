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

<body class="bg-[#0a0a0a] text-white min-h-screen flex">

  {{-- ── SIDEBAR ────────────────────────────────────────────────────── --}}
  <aside class="w-64 min-h-screen bg-[#0D3B2E] border-r border-white/5
                flex flex-col fixed top-0 left-0 z-30">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/5">
      <div class="w-9 h-9 rounded-full border border-[#4CAF82] bg-white/5
                  flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 text-[#4CAF82]" fill="none" stroke="currentColor"
             viewBox="0 0 24 24" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0
                   013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824
                   10.29 9 11.623 5.176-1.332 9-6.03 9-11.622
                   0-1.31-.21-2.571-.598-3.751h-.152c-3.196
                   0-6.1-1.248-8.25-3.285z"/>
        </svg>
      </div>
      <div>
        <p class="text-white font-bold text-sm leading-none">SmartGate</p>
        <p class="text-white/40 text-xs mt-0.5">ACC Admin Dashboard</p>
      </div>
    </div>

    {{-- Role Badge --}}
    <div class="mx-4 mt-4 mb-2 px-3 py-2 rounded-lg
                bg-[#4CAF82]/10 border border-[#4CAF82]/20">
      <p class="text-[10px] text-white/40 uppercase tracking-widest">Logged in as</p>
      <p class="text-[#4CAF82] font-semibold text-sm mt-0.5">
        {{ Auth::user()->full_name }}
      </p>
      <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-wider
                   px-2 py-0.5 rounded-full
                   {{ Auth::user()->isHR() ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
        {{ strtoupper(Auth::user()->role) }}
      </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

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
          <p class="text-[10px] text-white/25 uppercase tracking-widest">
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
          <p class="text-[10px] text-white/25 uppercase tracking-widest">
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
    <div class="p-4 border-t border-white/5">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl
                       text-white/40 hover:text-red-400 hover:bg-red-500/10
                       transition-all duration-200 text-sm group">
          <svg class="w-4 h-4" fill="none" stroke="currentColor"
               viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25
                     0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25
                     2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
          </svg>
          Sign Out
        </button>
      </form>
    </div>
  </aside>

  {{-- ── MAIN CONTENT ───────────────────────────────────────────────── --}}
  <div class="flex-1 ml-64 flex flex-col min-h-screen">

    {{-- Topbar --}}
    <header class="sticky top-0 z-20 bg-[#0a0a0a]/80 backdrop-blur-md
                   border-b border-white/5 px-8 py-4 flex items-center gap-4">
      <div class="flex-1">
        <h1 class="text-white font-semibold text-lg">
          @yield('page-title', 'Dashboard')
        </h1>
        <p class="text-white/30 text-xs mt-0.5">
          @yield('page-subtitle', '')
        </p>
      </div>

      {{-- Current date --}}
      <div class="text-right hidden sm:block">
        <p class="text-white/60 text-sm font-medium" id="topbar-time">--:-- --</p>
        <p class="text-white/30 text-xs" id="topbar-date">Loading...</p>
      </div>
    </header>

    {{-- Page Content --}}
    <main class="flex-1 p-8">
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
