{{-- resources/views/components/nav-item.blade.php --}}
{{-- SmartGate ACC — Reusable Sidebar Nav Item Component --}}

@props(['route', 'icon'])

@php
  $isActive = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<a href="{{ route($route) }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm
          transition-all duration-200
          {{ $isActive
            ? 'bg-[#4CAF82]/15 text-[#4CAF82] font-semibold border border-[#4CAF82]/20'
            : 'text-white/50 hover:text-white hover:bg-white/5' }}">

  {{-- Icon --}}
  <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
       viewBox="0 0 24 24" stroke-width="2">
    @switch($icon)
      @case('squares-2x2')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0
                 018.25 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25
                 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016
                 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0
                 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5
                 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25
                 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0
                 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25
                 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25
                 2.25 0 0113.5 18v-2.25z"/>
        @break
      @case('clock')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        @break
      @case('paper-airplane')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6 12L3.269 3.126A59.768 59.768 0 0121.485
                 12 59.77 59.77 0 013.27 20.876L5.999
                 12zm0 0h7.5"/>
        @break
      @case('map-pin')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M19.5 10.5c0 7.142-7.5 11.25-7.5
                 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
        @break
      @case('calendar-days')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25
                 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121
                 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25
                 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0
                 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008
                 v.008H12v-.008zM12 15h.008v.008H12V15zm0
                 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0
                 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0
                 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0
                 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008
                 v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"/>
        @break
      @case('document-chart-bar')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5
                 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25
                 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013
                 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25
                 2.25 0 003 5.25m18 0H3"/>
        @break
      @case('users')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337
                 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15
                 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15
                 19.128v.106A12.318 12.318 0 018.624 21c-2.331
                 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375
                 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75
                 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625
                 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        @break
      @case('device-tablet')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0
                 002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75A2.25
                 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z"/>
        @break
      @case('bell')
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967
                 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967
                 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085
                 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714
                 0m5.714 0a3 3 0 11-5.714 0"/>
        @break
    @endswitch
  </svg>

  {{-- Label + slot (for badge) --}}
  <span class="flex-1 flex items-center gap-2">
    {{ $slot }}
  </span>
</a>
