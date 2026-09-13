@extends('layouts.app')

@section('title', 'Import Employees')
@section('page-title', 'Bulk Employee Import')
@section('page-subtitle', 'Upload a CSV file to add multiple employees at once')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <a href="{{ route('employees.index') }}"
       class="text-slate-400 hover:text-slate-900 text-xs font-medium transition-colors flex items-center gap-1.5 w-fit">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Employees
    </a>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm rounded-xl px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('csv_file'))
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3">
            {{ $errors->first('csv_file') }}
        </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <p class="text-amber-600 text-xs uppercase tracking-wider mb-2">Import Warnings</p>
            <ul class="text-amber-700 text-xs space-y-1 list-disc list-inside">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
        <p class="text-slate-900 font-semibold text-sm mb-3">How it works</p>
        <ul class="text-slate-500 text-xs space-y-1.5 list-disc list-inside">
            <li>Upload a <span class="font-mono text-slate-700">.csv</span> file with employee records</li>
            <li>Required columns: <span class="font-mono text-slate-700">employee_code, last_name, first_name, department, employment_type, position</span></li>
            <li>Optional column: <span class="font-mono text-slate-700">middle_name</span></li>
            <li><span class="font-mono text-slate-700">employment_type</span> must be exactly <span class="font-mono text-slate-700">faculty</span> or <span class="font-mono text-slate-700">non-teaching</span></li>
            <li>Duplicate or existing employee codes will be skipped, not overwritten</li>
        </ul>

        <a href="{{ route('employees.import.template') }}"
           class="inline-flex items-center gap-1.5 mt-4 text-emerald-600 text-xs font-medium hover:underline">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Download sample CSV template
        </a>
    </div>

    <div class="bg-white border border-[#1e2a5e]/30 rounded-2xl p-6 shadow-sm">
        <form method="POST" action="{{ route('employees.import.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-slate-500 text-xs uppercase tracking-wider mb-2">
                    CSV File <span class="text-red-500">*</span>
                </label>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5
                              text-slate-900 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg
                              file:border-0 file:bg-emerald-50 file:text-emerald-600 file:text-xs
                              file:font-medium focus:outline-none focus:border-[#1e2a5e]/50">
            </div>

            <button type="submit"
                    class="bg-[#1e2a5e] hover:bg-[#141d47] text-white px-5 py-2.5 rounded-xl
                           text-sm font-semibold transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" transform="rotate(180 12 12)"/>
                </svg>
                Upload and Import
            </button>
        </form>
    </div>
</div>
@endsection