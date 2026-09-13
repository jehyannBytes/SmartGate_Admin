
Create.blade · PHP
@extends('layouts.app')
@section('title', 'Add Employee')
@section('page-title', 'Add Employee')
@section('page-subtitle', 'Register a new employee record')
 
@section('content')
<div class="max-w-2xl">
  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-8 shadow-sm">
    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" class="space-y-5">
      @csrf
 
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Last Name <span class="text-red-500">*</span></label>
          <input type="text" name="last_name" value="{{ old('last_name') }}"
                 class="w-full bg-white border rounded-xl px-4 py-2.5 text-slate-900 text-sm
                        focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40 focus:border-transparent
                        @error('last_name') border-red-400 @else border-slate-200 @enderror"/>
          @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">First Name <span class="text-red-500">*</span></label>
          <input type="text" name="first_name" value="{{ old('first_name') }}"
                 class="w-full bg-white border rounded-xl px-4 py-2.5 text-slate-900 text-sm
                        focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40 focus:border-transparent
                        @error('first_name') border-red-400 @else border-slate-200 @enderror"/>
          @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
 
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Middle Name</label>
        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
               class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5
                      text-slate-900 text-sm focus:outline-none focus:ring-2
                      focus:ring-[#1e2a5e]/40 focus:border-transparent"/>
      </div>
 
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Employee Code <span class="text-red-500">*</span></label>
        <input type="text" name="employee_code" value="{{ old('employee_code') }}"
               placeholder="e.g. EMP-2024-001"
               class="w-full bg-white border rounded-xl px-4 py-2.5 text-slate-900 text-sm
                      font-mono focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40
                      focus:border-transparent
                      @error('employee_code') border-red-400 @else border-slate-200 @enderror"/>
        @error('employee_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
 
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Position <span class="text-red-500">*</span></label>
        <input type="text" name="position" value="{{ old('position') }}"
               placeholder="e.g. Teacher I, Admin Staff"
               class="w-full bg-white border rounded-xl px-4 py-2.5 text-slate-900 text-sm
                      focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40 focus:border-transparent
                      @error('position') border-red-400 @else border-slate-200 @enderror"/>
        @error('position')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
 
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Department <span class="text-red-500">*</span></label>
        <input type="text" name="department" value="{{ old('department') }}"
               placeholder="e.g. College of Education"
               class="w-full bg-white border rounded-xl px-4 py-2.5 text-slate-900 text-sm
                      focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40 focus:border-transparent
                      @error('department') border-red-400 @else border-slate-200 @enderror"/>
        @error('department')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
 
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Employment Type <span class="text-red-500">*</span></label>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="employment_type" value="faculty"
                   {{ old('employment_type') === 'faculty' ? 'checked' : '' }}
                   class="accent-[#1e2a5e]"/>
            <span class="text-slate-700 text-sm">Faculty</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="employment_type" value="non-teaching"
                   {{ old('employment_type') === 'non-teaching' ? 'checked' : '' }}
                   class="accent-[#1e2a5e]"/>
            <span class="text-slate-700 text-sm">Non-Teaching</span>
          </label>
        </div>
        @error('employment_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
 
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">
          Employee Photo <span class="text-slate-400 text-xs">(clear, front-facing - used for face verification)</span>
        </label>
 
        <div class="flex items-center gap-4">
          <div id="photo-preview-wrap"
               class="w-20 h-20 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
            <img id="photo-preview" src="" alt="" class="w-full h-full object-cover hidden"/>
            <svg id="photo-placeholder" class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
          </div>
 
          <div class="flex-1">
            <input type="file" name="photo" id="photo-input" accept="image/png, image/jpeg"
                   class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0 file:text-sm file:font-medium
                          file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100
                          file:cursor-pointer cursor-pointer
                          @error('photo') border border-red-400 rounded-lg @enderror"/>
            <p class="text-slate-400 text-xs mt-1.5">JPG or PNG, max 5MB. Face must be clear and unobstructed.</p>
            @error('photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
        </div>
      </div>
 
      <div class="flex items-center gap-3 pt-2">
        <button type="submit"
                class="bg-[#1e2a5e] hover:bg-[#141d47] text-white font-semibold
                       px-6 py-2.5 rounded-xl text-sm transition-colors">
          Save Employee
        </button>
        <a href="{{ route('employees.index') }}"
           class="text-slate-400 hover:text-slate-900 text-sm transition-colors px-4 py-2.5">
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
 
