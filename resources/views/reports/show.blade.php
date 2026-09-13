@extends('layouts.app')
@section('title', 'CS Form 48 Reports — ' . $monthName)

@section('content')
<style>
@media print {
    body * { visibility: hidden; }
    .print-area, .print-area * { visibility: visible; }
    .print-area { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>

<div class="space-y-6">
    <div class="bg-white border rounded-2xl p-6 shadow-sm flex items-center justify-between no-print">
        <div>
            <h1 class="text-xl font-bold text-slate-900">CS Form 48 (Daily Time Records)</h1>
            <p class="text-slate-500 text-sm">{{ $monthName }} · {{ $report->department }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.export-pdf', $report) }}" class="bg-white hover:bg-slate-50 border px-4 py-2 rounded-xl text-sm font-medium">Export PDF</a>
            <button onclick="window.print()" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-xl text-sm font-semibold">Print DTRs</button>
            <a href="{{ route('reports.index') }}" class="text-slate-500 text-sm hover:underline">← Back</a>
        </div>
    </div>

    <div class="print-area grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($reportData as $emp)
            <div class="bg-white border p-6 rounded-xl text-[11px] shadow-sm font-sans space-y-3 page-break-after">
                <div class="text-center">
                    <span class="text-[9px] font-bold block text-left">CS FORM 48</span>
                    <h2 class="font-bold text-sm">DAILY TIME RECORD</h2>
                    <p class="font-bold border-b border-black inline-block px-4 mt-2 uppercase text-xs">{{ $emp['name'] }}</p>
                    <div class="mt-2 text-left space-y-0.5">
                        <p>For the month of: <span class="font-bold border-b border-black">{{ $monthName }}</span></p>
                        <p>Official hours for arrival and departure:</p>
                        <p class="pl-2">Regular days: <span class="border-b border-black">8:00 A.M. - 5:00 P.M.</span></p>
                        <p class="pl-2">Saturdays: <span class="border-b border-black">As required</span></p>
                    </div>
                </div>

                <table class="w-full border-collapse border border-black text-center text-[10px]">
                    <thead>
                        <tr class="border-b border-black">
                            <th rowspan="2" class="border-r border-black w-8">DAY</th>
                            <th colspan="2" class="border-r border-black">AM</th>
                            <th colspan="2" class="border-r border-black">PM</th>
                            <th colspan="2">UNDERTIME</th>
                        </tr>
                        <tr class="border-b border-black">
                            <th class="border-r border-black font-normal">Arrival</th>
                            <th class="border-r border-black font-normal">Departure</th>
                            <th class="border-r border-black font-normal">Arrival</th>
                            <th class="border-r border-black font-normal">Departure</th>
                            <th class="border-r border-black font-normal">Hours</th>
                            <th class="font-normal">Mins</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emp['daily_logs'] as $log)
                            <tr class="border-b border-black h-4">
                                <td class="border-r border-black font-bold">{{ $log['day'] }}</td>
                                @if($log['is_weekend'])
                                    <td colspan="6" class="bg-slate-100 font-bold tracking-widest text-[9px] uppercase">{{ $log['label'] }}</td>
                                @else
                                    <td class="border-r border-black">{{ $log['morning_in'] }}</td>
                                    <td class="border-r border-black">{{ $log['morning_out'] }}</td>
                                    <td class="border-r border-black">{{ $log['afternoon_in'] }}</td>
                                    <td class="border-r border-black">{{ $log['afternoon_out'] }}</td>
                                    <td class="border-r border-black">{{ $log['undertime_hours'] }}</td>
                                    <td>{{ $log['undertime_minutes'] }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-bold border-t border-black">
                            <td colspan="5" class="text-right pr-2 border-r border-black">TOTAL</td>
                            <td class="border-r border-black">{{ $emp['total_undertime_hours'] }}</td>
                            <td>{{ $emp['total_undertime_minutes'] }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-[9px] text-justify pt-1 space-y-3">
                    <p>I CERTIFY on my honor that the above is a true and correct report of hours of work performed, record of which was made daily at the time of arrival and departure from office.</p>
                    <div class="text-center pt-3">
                        <p class="font-bold border-b border-black inline-block px-8 uppercase">{{ $emp['name'] }}</p>
                    </div>
                    <p class="text-[8px]">VERIFIED as to the prescribed office hours:</p>
                    <div class="text-center pt-2">
                        <div class="border-b border-black w-2/3 mx-auto"></div>
                        <p class="text-[8px] mt-0.5">In-Charge</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection