@extends('layouts.app')
@section('title', 'Enroll Face')
@section('page-title', 'Face Enrollment')
@section('page-subtitle', 'Live capture for ' . $employee->full_name)

@section('content')
<div class="max-w-3xl">

  @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3 mb-5">
      {{ session('error') }}
    </div>
  @endif

  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-8 shadow-sm">

    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-slate-900 font-semibold text-lg">{{ $employee->full_name }}</h3>
        <p class="text-slate-400 text-sm">{{ $employee->employee_code }} . {{ $employee->position }}</p>
      </div>
      <div class="text-right">
        <p class="text-slate-400 text-xs">Captures</p>
        <p class="text-2xl font-bold text-emerald-600">
          <span id="capture-count">{{ $existingCount }}</span><span class="text-slate-400 text-base"> / {{ $minCaptures }} min</span>
        </p>
      </div>
    </div>

    <div class="relative bg-slate-900 rounded-2xl overflow-hidden aspect-video mb-4">
      <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover -scale-x-100"></video>
      <canvas id="capture-canvas" class="hidden"></canvas>

      <div id="camera-error" class="hidden absolute inset-0 flex items-center justify-center bg-slate-900 text-center p-6">
        <p class="text-red-400 text-sm">
          Could not access webcam. Please allow camera permission and reload the page.
        </p>
      </div>

      <div id="face-guide" class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="w-48 h-60 border-2 border-white/40 rounded-[50%]"></div>
      </div>

      <div id="flash-overlay" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-150"></div>
    </div>

    <div class="flex items-center justify-between mb-6">
      <p id="capture-status" class="text-slate-500 text-sm">
        Position your face inside the oval, then click Capture. Slightly turn your head between shots (left, right, up, down) for better accuracy.
      </p>
    </div>

    <div class="flex items-center gap-3 mb-8">
      <button type="button" id="capture-btn"
              class="bg-[#1e2a5e] hover:bg-[#141d47] text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.174C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.174 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.822 1.316z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
        </svg>
        Capture Shot
      </button>
      <span id="capture-hint" class="text-slate-400 text-xs"></span>
    </div>

    <div>
      <p class="text-slate-600 text-sm font-medium mb-3">Captured Photos</p>
      <div id="thumbnails" class="grid grid-cols-5 gap-3">
        @foreach($employee->faceCaptures as $capture)
          <div class="relative group aspect-square rounded-xl overflow-hidden border border-slate-200" data-capture-id="{{ $capture->capture_id }}">
            <img src="{{ asset($capture->image_path) }}" class="w-full h-full object-cover"/>
            <button type="button" class="delete-capture-btn absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity"
                    data-capture-id="{{ $capture->capture_id }}">
              <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        @endforeach
      </div>
      <p id="empty-hint" class="text-slate-400 text-xs mt-2 @if($existingCount > 0) hidden @endif">
        No photos captured yet.
      </p>
    </div>

    <div class="flex items-center gap-3 pt-8 mt-2 border-t border-slate-200">
      <form method="POST" action="{{ route('employees.enroll-face.complete', $employee) }}">
        @csrf
        <button type="submit" id="finish-btn"
                class="bg-[#1e2a5e] hover:bg-[#141d47] disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed
                       text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors"
                {{ $existingCount >= $minCaptures ? '' : 'disabled' }}>
          Finish Enrollment
        </button>
      </form>
      <a href="{{ route('employees.show', $employee) }}"
         class="text-slate-400 hover:text-slate-900 text-sm transition-colors px-4 py-2.5">
        Skip for now
      </a>
    </div>

  </div>
</div>

<script>
(function () {
  const MIN_CAPTURES = {{ $minCaptures }};
  const STORE_URL   = "{{ route('employees.enroll-face.store', $employee) }}";
  const DELETE_URL_BASE = "{{ route('employees.enroll-face.destroy', [$employee, '__ID__']) }}";
  const CSRF_TOKEN  = "{{ csrf_token() }}";

  const video        = document.getElementById('webcam');
  const canvas       = document.getElementById('capture-canvas');
  const captureBtn   = document.getElementById('capture-btn');
  const countLabel   = document.getElementById('capture-count');
  const thumbnails   = document.getElementById('thumbnails');
  const emptyHint    = document.getElementById('empty-hint');
  const finishBtn    = document.getElementById('finish-btn');
  const flashOverlay = document.getElementById('flash-overlay');
  const cameraError  = document.getElementById('camera-error');
  const statusText   = document.getElementById('capture-status');

  let currentCount = {{ $existingCount }};

  navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 }, audio: false })
    .then(stream => {
      video.srcObject = stream;
    })
    .catch(err => {
      console.error('Webcam error:', err);
      cameraError.classList.remove('hidden');
      captureBtn.disabled = true;
    });

  captureBtn.addEventListener('click', function () {
    if (!video.videoWidth) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');

    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

    flashOverlay.style.opacity = '0.7';
    setTimeout(() => { flashOverlay.style.opacity = '0'; }, 150);

    captureBtn.disabled = true;
    statusText.textContent = 'Uploading...';

    fetch(STORE_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ image: dataUrl }),
    })
      .then(res => res.json())
      .then(data => {
        captureBtn.disabled = false;
        if (!data.success) {
          statusText.textContent = data.message || 'Capture failed. Please try again.';
          return;
        }

        currentCount = data.total_count;
        countLabel.textContent = currentCount;
        emptyHint.classList.add('hidden');

        addThumbnail(data.capture_id, data.image_url);

        if (currentCount >= MIN_CAPTURES) {
          finishBtn.disabled = false;
          statusText.textContent = 'Great! You have enough photos. Capture a few more for better accuracy, or click Finish Enrollment.';
        } else {
          const remaining = MIN_CAPTURES - currentCount;
          statusText.textContent = `Good shot! Turn your head slightly and capture ${remaining} more.`;
        }
      })
      .catch(err => {
        console.error('Upload error:', err);
        captureBtn.disabled = false;
        statusText.textContent = 'Upload failed. Check your connection and try again.';
      });
  });

  function addThumbnail(captureId, imageUrl) {
    const div = document.createElement('div');
    div.className = 'relative group aspect-square rounded-xl overflow-hidden border border-slate-200';
    div.dataset.captureId = captureId;
    div.innerHTML = `
      <img src="${imageUrl}" class="w-full h-full object-cover"/>
      <button type="button" class="delete-capture-btn absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity" data-capture-id="${captureId}">
        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    `;
    thumbnails.appendChild(div);
  }

  thumbnails.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-capture-btn');
    if (!btn) return;

    const captureId = btn.dataset.captureId;
    const url = DELETE_URL_BASE.replace('__ID__', captureId);

    fetch(url, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json',
      },
    })
      .then(res => res.json())
      .then(data => {
        if (!data.success) return;

        const el = thumbnails.querySelector(`[data-capture-id="${captureId}"]`);
        if (el) el.remove();

        currentCount = data.total_count;
        countLabel.textContent = currentCount;

        if (currentCount === 0) {
          emptyHint.classList.remove('hidden');
        }
        if (currentCount < MIN_CAPTURES) {
          finishBtn.disabled = true;
        }
      })
      .catch(err => console.error('Delete error:', err));
  });

  window.addEventListener('beforeunload', function () {
    const stream = video.srcObject;
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
  });
})();
</script>
@endsection