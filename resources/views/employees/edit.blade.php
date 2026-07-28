@extends('layouts.app')
@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')
@section('page-subtitle', 'Update employee record - ' . $employee->full_name)

@section('content')
<div class="max-w-2xl">
  <div class="bg-white/5 border border-white/10 rounded-2xl p-8">
    <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data" class="space-y-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-white/60 mb-1.5">Last Name <span class="text-red-400">*</span></label>
          <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}"
                 class="w-full bg-white/5 border rounded-xl px-4 py-2.5 text-white text-sm
                        focus:outline-none focus:ring-2 focus:ring-[#4CAF82] focus:border-transparent
                        @error('last_name') border-red-400 @else border-white/10 @enderror"/>
          @error('last_name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-white/60 mb-1.5">First Name <span class="text-red-400">*</span></label>
          <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                 class="w-full bg-white/5 border rounded-xl px-4 py-2.5 text-white text-sm
                        focus:outline-none focus:ring-2 focus:ring-[#4CAF82] focus:border-transparent
                        @error('first_name') border-red-400 @else border-white/10 @enderror"/>
          @error('first_name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-white/60 mb-1.5">Middle Name</label>
        <input type="text" name="middle_name" value="{{ old('middle_name', $employee->middle_name) }}"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5
                      text-white text-sm focus:outline-none focus:ring-2
                      focus:ring-[#4CAF82] focus:border-transparent"/>
      </div>

      <div>
        <label class="block text-sm font-medium text-white/60 mb-1.5">Employee Code <span class="text-red-400">*</span></label>
        <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}"
               class="w-full bg-white/5 border rounded-xl px-4 py-2.5 text-white text-sm
                      font-mono focus:outline-none focus:ring-2 focus:ring-[#4CAF82]
                      focus:border-transparent
                      @error('employee_code') border-red-400 @else border-white/10 @enderror"/>
        @error('employee_code')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-white/60 mb-1.5">Position <span class="text-red-400">*</span></label>
        <input type="text" name="position" value="{{ old('position', $employee->position) }}"
               class="w-full bg-white/5 border rounded-xl px-4 py-2.5 text-white text-sm
                      focus:outline-none focus:ring-2 focus:ring-[#4CAF82] focus:border-transparent
                      @error('position') border-red-400 @else border-white/10 @enderror"/>
        @error('position')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-white/60 mb-1.5">Department <span class="text-red-400">*</span></label>
        <input type="text" name="department" value="{{ old('department', $employee->department) }}"
               class="w-full bg-white/5 border rounded-xl px-4 py-2.5 text-white text-sm
                      focus:outline-none focus:ring-2 focus:ring-[#4CAF82] focus:border-transparent
                      @error('department') border-red-400 @else border-white/10 @enderror"/>
        @error('department')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-white/60 mb-1.5">Employment Type <span class="text-red-400">*</span></label>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="employment_type" value="faculty"
                   {{ old('employment_type', $employee->employment_type) === 'faculty' ? 'checked' : '' }}
                   class="accent-[#4CAF82]"/>
            <span class="text-white text-sm">Faculty</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="employment_type" value="non-teaching"
                   {{ old('employment_type', $employee->employment_type) === 'non-teaching' ? 'checked' : '' }}
                   class="accent-[#4CAF82]"/>
            <span class="text-white text-sm">Non-Teaching</span>
          </label>
        </div>
        @error('employment_type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="hidden" name="is_active" value="0"/>
          <input type="checkbox" name="is_active" value="1"
                 {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                 class="w-4 h-4 accent-[#4CAF82]"/>
          <span class="text-white text-sm">Active Employee</span>
        </label>
      </div>

      <div>
        <label class="block text-sm font-medium text-white/60 mb-1.5">
          Employee Photo <span class="text-white/30 text-xs">(clear, front-facing - used for face verification)</span>
        </label>

        <div class="flex items-center gap-4">
          <div id="photo-preview-wrap"
               class="w-20 h-20 rounded-xl border border-white/10 bg-white/5 flex items-center justify-center overflow-hidden shrink-0">
            @if($employee->photo_url)
              <img id="photo-preview" src="{{ asset($employee->photo_url) }}" alt="" class="w-full h-full object-cover"/>
              <svg id="photo-placeholder" class="w-8 h-8 text-white/20 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
              </svg>
            @else
              <img id="photo-preview" src="" alt="" class="w-full h-full object-cover hidden"/>
              <svg id="photo-placeholder" class="w-8 h-8 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
              </svg>
            @endif
          </div>

          <div class="flex-1">
            <input type="file" name="photo" id="photo-input" accept="image/png, image/jpeg"
                   class="w-full text-sm text-white/60 file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0 file:text-sm file:font-medium
                          file:bg-[#4CAF82]/15 file:text-[#4CAF82] hover:file:bg-[#4CAF82]/25
                          file:cursor-pointer cursor-pointer
                          @error('photo') border border-red-400 rounded-lg @enderror"/>
            <p class="text-white/30 text-xs mt-1.5">JPG or PNG, max 5MB. Leave blank to keep current photo.</p>
            @error('photo')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button type="submit"
                class="bg-[#4CAF82] hover:bg-[#3d9e71] text-white font-semibold
                       px-6 py-2.5 rounded-xl text-sm transition-colors">
          Update Employee
        </button>
        <a href="{{ route('employees.show', $employee) }}"
           class="text-white/40 hover:text-white text-sm transition-colors px-4 py-2.5">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  document.getElementById('photo-input').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photo-preview');
    const placeholder = document.getElementById('photo-placeholder');
    if (file) {
      const reader = new FileReader();
      reader.onload = ev => {
        preview.src = ev.target.result;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
      };
      reader.readAsDataURL(file);
    }
  });
</script>
@endsection