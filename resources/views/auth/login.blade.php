{{-- resources/views/auth/login.blade.php --}}
{{-- SmartGate ACC — Admin Dashboard Login Page --}}
{{-- Laravel Breeze + Blade + Tailwind CSS --}}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartGate ACC — Admin Login</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#0D3B2E] flex items-center justify-center">

  {{-- Background subtle grid --}}
  <div class="absolute inset-0 opacity-5"
       style="background-image: radial-gradient(#4CAF82 1px, transparent 1px);
              background-size: 32px 32px;">
  </div>

  {{-- Login Card --}}
  <div class="relative z-10 w-full max-w-md mx-4">

    {{-- Logo + App Name --}}
    <div class="flex flex-col items-center mb-8">
      <div class="w-20 h-20 rounded-full border-2 border-[#4CAF82]
                  bg-white/10 flex items-center justify-center mb-4
                  shadow-[0_0_40px_rgba(76,175,130,0.2)]">
        {{-- Replace with actual logo --}}
        <svg class="w-10 h-10 text-[#4CAF82]" fill="none" stroke="currentColor"
             viewBox="0 0 24 24" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0
                   013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824
                   10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21
                   -2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
        </svg>
      </div>
      <h1 class="text-white text-3xl font-bold tracking-wide">SmartGate</h1>
      <p class="text-white/40 text-sm tracking-widest uppercase mt-1">
        Admin Dashboard
      </p>
    </div>

    {{-- Card --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-8
                backdrop-blur-sm shadow-2xl">

      {{-- Card Header --}}
      <div class="mb-6">
        <h2 class="text-white text-xl font-semibold">Sign in to your account</h2>
        <p class="text-white/40 text-sm mt-1">
          For authorized HR and ICTMO personnel only.
        </p>
      </div>

      {{-- Session Error (Breeze) --}}
      @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-[#4CAF82]/10 border border-[#4CAF82]/30
                    text-[#4CAF82] text-sm">
          {{ session('status') }}
        </div>
      @endif

      {{-- Login Form --}}
      <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Username --}}
        <div>
          <label for="username"
                 class="block text-sm font-medium text-white/60 mb-1.5">
            Username
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center
                        pointer-events-none">
              <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor"
                   viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0
                         017.5 0zM4.501 20.118a7.5 7.5 0 0114.998
                         0A17.933 17.933 0 0112 21.75c-2.676
                         0-5.216-.584-7.499-1.632z"/>
              </svg>
            </div>
            <input
              id="username"
              name="username"
              type="text"
              value="{{ old('username') }}"
              required
              autofocus
              autocomplete="username"
              placeholder="Enter your username"
              class="w-full bg-white/5 border rounded-xl pl-10 pr-4 py-3
                     text-white placeholder-white/20 text-sm
                     focus:outline-none focus:ring-2 focus:ring-[#4CAF82]
                     focus:border-transparent transition-all
                     @error('username') border-red-400 @else border-white/10 @enderror"
            />
          </div>
          @error('username')
            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
          @enderror
        </div>

        {{-- Password --}}
        <div>
          <label for="password"
                 class="block text-sm font-medium text-white/60 mb-1.5">
            Password
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center
                        pointer-events-none">
              <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor"
                   viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75
                         11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25
                         2.25 0 00-2.25-2.25H6.75a2.25 2.25 0
                         00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
              </svg>
            </div>
            <input
              id="password"
              name="password"
              type="password"
              required
              autocomplete="current-password"
              placeholder="Enter your password"
              class="w-full bg-white/5 border rounded-xl pl-10 pr-12 py-3
                     text-white placeholder-white/20 text-sm
                     focus:outline-none focus:ring-2 focus:ring-[#4CAF82]
                     focus:border-transparent transition-all
                     @error('password') border-red-400 @else border-white/10 @enderror"
            />
            {{-- Toggle password visibility --}}
            <button type="button"
                    onclick="togglePassword()"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center
                           text-white/30 hover:text-white/60 transition-colors">
              <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor"
                   viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.036 12.322a1.012 1.012 0 010-.639C3.423
                         7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007
                         9.963 7.178.07.207.07.431 0 .639C20.577
                         16.49 16.64 19.5 12 19.5c-4.638
                         0-8.573-3.007-9.963-7.178z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </button>
          </div>
          @error('password')
            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
          @enderror
        </div>

        {{-- Role indicator (read-only, determined by account) --}}
        <div class="flex items-center gap-2 p-3 rounded-xl bg-[#4CAF82]/5
                    border border-[#4CAF82]/20">
          <svg class="w-4 h-4 text-[#4CAF82] shrink-0" fill="none"
               stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708
                     2.836a.75.75 0 001.063.853l.041-.021M21 12a9
                     9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
          </svg>
          <p class="text-xs text-white/40">
            Your role (HR or ICTMO) is determined by your account.
            Contact ICTMO if you cannot log in.
          </p>
        </div>

        {{-- Submit Button --}}
        <button
          type="submit"
          class="w-full bg-[#4CAF82] hover:bg-[#3d9e71] active:bg-[#2e8a5e]
                 text-white font-semibold py-3 px-4 rounded-xl
                 transition-all duration-200 flex items-center justify-center
                 gap-2 shadow-[0_0_20px_rgba(76,175,130,0.3)]
                 hover:shadow-[0_0_30px_rgba(76,175,130,0.4)]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor"
               viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25
                     2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5
                     21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3
                     3m0 0l3 3m-3-3h12.75"/>
          </svg>
          Sign In
        </button>
      </form>
    </div>

    {{-- Footer --}}
    <p class="text-center text-white/20 text-xs mt-6">
      Agusan del Norte College of Commerce — SmartGate ACC v1.0
    </p>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>

</body>
</html>
