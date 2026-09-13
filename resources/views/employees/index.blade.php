@extends('layouts.app')
@section('title', 'Employees')
@section('page-title', 'Employee Management')
@section('page-subtitle', 'Manage all employee records — ' . $employees->total() . ' total employees')

@section('content')
<div class="space-y-4">

  {{-- Toolbar --}}
  <div class="flex flex-wrap items-center gap-3">
    {{-- Search --}}
    <form method="GET" action="{{ route('employees.index') }}"
          class="flex flex-wrap items-center gap-3 flex-1">

      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300"
             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
        </svg>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name, code, position..."
               class="bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2.5
                      text-slate-900 placeholder-slate-400 text-sm w-72
                      focus:outline-none focus:ring-2 focus:ring-[#1e2a5e]/40 focus:border-transparent"/>
      </div>

      {{-- Department Filter --}}
      <select name="department"
              class="bg-white border border-slate-200 rounded-xl px-4 py-2.5
                     text-slate-700 text-sm focus:outline-none focus:ring-2
                     focus:ring-[#1e2a5e]/40 focus:border-transparent">
        <option value="">All Departments</option>
        @foreach($departments as $dept)
          <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
            {{ $dept }}
          </option>
        @endforeach
      </select>

      {{-- Employment Type Filter --}}
      <select name="employment_type"
              class="bg-white border border-slate-200 rounded-xl px-4 py-2.5
                     text-slate-700 text-sm focus:outline-none focus:ring-2
                     focus:ring-[#1e2a5e]/40 focus:border-transparent">
        <option value="">All Types</option>
        <option value="faculty" {{ request('employment_type') === 'faculty' ? 'selected' : '' }}>Faculty</option>
        <option value="non-teaching" {{ request('employment_type') === 'non-teaching' ? 'selected' : '' }}>Non-Teaching</option>
      </select>

      {{-- Status Filter --}}
      <select name="status"
              class="bg-white border border-slate-200 rounded-xl px-4 py-2.5
                     text-slate-700 text-sm focus:outline-none focus:ring-2
                     focus:ring-[#1e2a5e]/40 focus:border-transparent">
        <option value="">All Status</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
      </select>

      <button type="submit"
              class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5
                     rounded-xl text-sm font-medium transition-colors">
        Filter
      </button>

      @if(request()->hasAny(['search', 'department', 'employment_type', 'status']))
        <a href="{{ route('employees.index') }}"
           class="text-slate-400 hover:text-slate-900 text-sm transition-colors">
          Clear
        </a>
      @endif
    </form>

    {{-- Add Employee Button --}}
    <a href="{{ route('employees.create') }}"
       class="bg-[#1e2a5e] hover:bg-[#141d47] text-white px-4 py-2.5
              rounded-xl text-sm font-semibold transition-colors flex items-center gap-2 shrink-0">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
      </svg>
      Add Employee
    </a>
  </div>

  {{-- Table --}}
  <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl overflow-hidden shadow-sm">
    <table class="w-full">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-50">
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Code</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Department</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
          <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
          <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($employees as $employee)
          <tr class="hover:bg-slate-50 transition-colors">
            {{-- Employee Name + Position --}}
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-50 border border-emerald-200
                            flex items-center justify-center shrink-0">
                  <span class="text-emerald-600 text-xs font-bold">
                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                  </span>
                </div>
                <div>
                  <p class="text-slate-900 text-sm font-semibold">
                    {{ $employee->last_name }}, {{ $employee->first_name }}
                    {{ $employee->middle_name ? strtoupper(substr($employee->middle_name, 0, 1)).'.' : '' }}
                  </p>
                  <p class="text-slate-400 text-xs">{{ $employee->position }}</p>
                </div>
              </div>
            </td>

            {{-- Code --}}
            <td class="px-6 py-4">
              <span class="text-slate-500 text-sm font-mono">{{ $employee->employee_code }}</span>
            </td>

            {{-- Department --}}
            <td class="px-6 py-4">
              <span class="text-slate-600 text-sm">{{ $employee->department }}</span>
            </td>

            {{-- Employment Type --}}
            <td class="px-6 py-4">
              @if($employee->employment_type === 'faculty')
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                             bg-blue-50 text-blue-600 border border-blue-200">
                  Faculty
                </span>
              @else
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                             bg-purple-50 text-purple-600 border border-purple-200">
                  Non-Teaching
                </span>
              @endif
            </td>

            {{-- Status --}}
            <td class="px-6 py-4">
              @if($employee->is_active)
                <span class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Active
                </span>
              @else
                <span class="flex items-center gap-1.5 text-xs font-semibold text-red-500">
                  <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                  Inactive
                </span>
              @endif
            </td>

            {{-- Actions --}}
            <td class="px-6 py-4">
              <div class="flex items-center justify-end gap-2">
                {{-- View --}}
                <a href="{{ route('employees.show', $employee) }}"
                   class="text-slate-400 hover:text-emerald-600 transition-colors p-1.5
                          rounded-lg hover:bg-emerald-50" title="View Profile">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5
                             12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431
                             0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638
                             0-8.573-3.007-9.963-7.178z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                </a>

                {{-- Edit --}}
                <a href="{{ route('employees.edit', $employee) }}"
                   class="text-slate-400 hover:text-blue-600 transition-colors p-1.5
                          rounded-lg hover:bg-blue-50" title="Edit">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652
                             2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6
                             18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0
                             0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75
                             21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0
                             015.25 6H10"/>
                  </svg>
                </a>

                {{-- Deactivate --}}
                @if($employee->is_active)
                  <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                        onsubmit="return confirm('Deactivate {{ $employee->first_name }} {{ $employee->last_name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-slate-400 hover:text-red-500 transition-colors p-1.5
                                   rounded-lg hover:bg-red-50" title="Deactivate">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 11-6.75
                                 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375
                                 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374
                                 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                      </svg>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
              No employees found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Pagination --}}
    @if($employees->hasPages())
      <div class="px-6 py-4 border-t border-slate-200">
        {{ $employees->links() }}
      </div>
    @endif
  </div>
</div>
@endsection