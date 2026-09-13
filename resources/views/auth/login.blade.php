{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartGate ACC — Admin Login</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
  <style>
    /* Slanted Diagonal Edge for Left Sidebar */
    @media (min-width: 80px) {
      .slanted-sidebar {
        clip-path: polygon(0 0, 100% 0, 88% 100%, 0 100%);
      }
    }

    /* Brand serif font for the SmartGate logo text — matches reference: 800 weight, Playfair Display */
    .brand-serif {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-weight: 800;
    }
  </style>
</head>

<body class="h-screen w-full flex flex-col md:flex-row bg-[#f8fafc] font-sans antialiased overflow-hidden">
{{-- LEFT SIDEBAR: Rich Navy Blue with Slanted Right Edge --}}
 <div class="w-full md:w-1/2 bg-[#16234f] text-white p-8 lg:p-10 flex flex-col justify-between relative overflow-hidden shrink-0 h-full slanted-sidebar z-10">
    
    {{-- 1. Campus Photo (bottom-right, fading smoothly into the navy on both top and left edges) --}}
    <div class="absolute bottom-0 right-0 w-full h-[100%] bg-cover bg-bottom bg-no-repeat opacity-45 z-0 pointer-events-none" 
       style="background-image: url('{{ asset('images/campus.png') }}');
              mask-image: linear-gradient(to top, rgba(0,0,0,1) 10%, rgba(0,0,0,0) 100%);
              -webkit-mask-image: linear-gradient(to top, rgba(0,0,0,1) 10%, rgba(0,0,0,0) 100%);">
    </div>

    {{-- 2. Very light overlay tint for cohesion with the navy palette --}}
    <div class="absolute inset-0 bg-[#16234f]/10 z-0"></div>

    

    {{-- TOP BRANDING AREA --}}
    <div class="relative z-10 my-auto flex flex-col justify-center space-y-4">
  <div class="flex items-center gap-4">
    <img src="{{ asset('images/logo.png') }}" alt="PSU Logo" class="w-20 h-20 object-contain drop-shadow-md" />
  </div>

  <div>
    <h1 class="brand-serif text-3xl lg:text-4xl tracking-tight text-white">
      Smart<span class="text-amber-400">Gate</span>
    </h1>
    <p class="text-amber-400/90 font-bold text-xs tracking-widest uppercase mt-0.5">
      ACC ADMIN DASHBOARD
    </p>
    <div class="w-12 h-1 bg-amber-400 mt-2 rounded-full"></div>
  </div>

  <p class="text-slate-300 text-xs lg:text-sm font-medium leading-relaxed max-w-sm">
    Smart Attendance. Secure Campus. Real-time Monitoring.
  </p>
</div>

    {{-- BOTTOM NOTICE BADGES --}}
    <div class="relative z-10 space-y-3 mt-4 max-w-sm">
      {{-- Secure Access Badge --}}
      <div class="bg-white/5 border border-white/10 backdrop-blur-md rounded-xl p-3 flex items-start gap-3">
        <div class="w-6 h-6 rounded-full bg-amber-400 flex items-center justify-center shrink-0 text-[#0B1935] font-bold mt-0.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
          </svg>
        </div>
        <div>
          <h4 class="text-xs font-bold text-white">Secure Access</h4>
          <p class="text-[10.5px] text-slate-300 mt-0.5">For authorized HR and ICTMO personnel only.</p>
        </div>
      </div>

      {{-- Restricted System Notice --}}
      <div class="bg-rose-500/10 border border-rose-500/20 backdrop-blur-md rounded-xl p-3 flex items-start gap-3">
        <div class="w-6 h-6 rounded-full bg-rose-500/80 flex items-center justify-center shrink-0 text-white font-bold mt-0.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM12 3a9 9 0 100 18 9 9 0 000-18z" />
          </svg>
        </div>
        <div>
          <h4 class="text-xs font-bold text-rose-300">Restricted System Notice</h4>
          <p class="text-[10px] text-rose-200/80 leading-relaxed mt-0.5">
            NOTICE: This is a restricted administrative system. Only authorized ICTMO and HR personnel may log in and operate this application. Violators may be subject to disciplinary action.
          </p>
        </div>
      </div>
    </div>

  </div>

  {{-- RIGHT CONTENT AREA --}}
  <div class="flex-1 flex flex-col justify-between p-4 lg:p-6 relative h-full overflow-hidden bg-[#f8fafc]">
    
    {{-- Live Clock --}}
    <div class="text-right">
      <div class="flex items-center justify-end gap-2">
        <h2 id="live-time" class="text-xl font-black text-[#0B1935]">--:--:-- --</h2>
        <div class="w-1 h-5 bg-amber-400 rounded-full"></div>
      </div>
      <p id="live-date" class="text-[11px] font-medium text-slate-400">------------------</p>
    </div>

    {{-- Login Form Box --}}
    <div class="w-full max-w-sm mx-auto bg-white border border-slate-200 rounded-2xl p-6 shadow-sm my-auto">
      
      <div class="mb-4">
        <h2 class="text-xl font-bold text-[#0B1935]">Welcome back!</h2>
        <div class="w-6 h-1 bg-amber-400 rounded-full mt-1 mb-1.5"></div>
        <p class="text-xs font-medium text-slate-600">Sign in to access your account</p>
        <p class="text-[10px] text-slate-400">For authorized HR and ICTMO personnel only.</p>
      </div>

      @if (session('status'))
        <div class="mb-3 p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="space-y-3">
        @csrf

        <div>
          <label for="username" class="block text-xs font-bold text-slate-700 mb-1">Username</label>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-purple-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
              </svg>
            </span>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus placeholder="Enter your username" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-slate-800 text-xs focus:bg-white focus:border-[#0B1935] outline-none" />
          </div>
        </div>

        <div>
          <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-amber-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
              </svg>
            </span>
            <input id="password" name="password" type="password" required placeholder="Enter your password" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-9 py-2 text-slate-800 text-xs focus:bg-white focus:border-[#0B1935] outline-none" />
            <button type="button" onclick="togglePassword()" class="absolute right-3 text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.573 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between text-xs pt-0.5">
          <label class="flex items-center gap-1.5 text-slate-600 font-medium cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#0B1935]">
            Remember me
          </label>
          <a href="#" class="text-[#0B1935] font-bold text-[10.5px]">Forgot password?</a>
        </div>

        <button type="submit" class="w-full bg-[#0B1935] hover:bg-[#12254c] text-white font-bold py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
          SIGN IN
        </button>
      </form>

     

    </div>

    <p class="text-center text-slate-400 text-[10px] font-medium">
      Pangasinan State University Alaminos City Campus — SmartGate ACC v1.0
    </p>

  </div>

  <script>
    function updateClock() {
      const now = new Date();
      const timeEl = document.getElementById('live-time');
      const dateEl = document.getElementById('live-date');
      if (timeEl) timeEl.textContent = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
      if (dateEl) dateEl.textContent = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    function togglePassword() {
      const input = document.getElementById('password');
      if (input) input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>

</body>
</html>