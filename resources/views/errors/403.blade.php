<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>403 — Forbidden</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0D3B2E] flex items-center justify-center">
  <div class="text-center">
    <p class="text-[#4CAF82] text-6xl font-bold mb-4">403</p>
    <p class="text-white text-xl font-semibold mb-2">Access Denied</p>
    <p class="text-white/40 text-sm mb-6">{{ $message ?? 'You do not have permission to access this page.' }}</p>
    <a href="{{ route('dashboard') }}" class="bg-[#4CAF82] text-white px-6 py-2 rounded-xl text-sm font-semibold hover:bg-[#3d9e71] transition-colors">
      Go to Dashboard
    </a>
  </div>
</body>
</html>
