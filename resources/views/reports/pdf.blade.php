<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 15px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8px; color: #000; }
        .page-container { page-break-after: always; width: 100%; }
        .dtr-table-wrapper { width: 48%; float: left; margin-right: 2%; }
        .dtr-table-wrapper:last-child { margin-right: 0; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        table.dtr-grid { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.dtr-grid th, table.dtr-grid td { border: 1px solid #000; text-align: center; height: 11px; padding: 0; }
        table.dtr-grid th { font-size: 7px; text-transform: uppercase; }
        
        .line-underline { border-bottom: 1px solid #000; display: inline-block; padding: 0 5px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    @foreach($reportData as $emp)
        <div class="page-container">
            @for($copy = 1; $copy <= 2; $copy++)
                <div class="dtr-table-wrapper">
                    <div style="font-size: 7px;" class="font-bold">CS FORM 48</div>
                    <div class="text-center font-bold" style="font-size: 11px;">DAILY TIME RECORD</div>
                    
                    <div class="text-center font-bold uppercase" style="margin-top: 8px; font-size: 10px;">
                        <span class="line-underline" style="width: 80%;">{{ $emp['name'] }}</span>
                    </div>

                    <div style="margin-top: 6px;">
                        For the month of: <span class="line-underline font-bold uppercase" style="width: 50%;">{{ $monthName }}</span><br>
                        Official hours for arrival and departure:<br>
                        Regular days: <span class="line-underline" style="width: 55%;">8:00 A.M. - 5:00 P.M.</span><br>
                        Saturdays: <span class="line-underline" style="width: 60%;">As required</span>
                    </div>

                    <table class="dtr-grid">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 10%;">DAY</th>
                                <th colspan="2" style="width: 30%;">A.M.</th>
                                <th colspan="2" style="width: 30%;">P.M.</th>
                                <th colspan="2" style="width: 30%;">UNDERTIME</th>
                            </tr>
                            <tr>
                                <th style="width: 15%;">Arrival</th>
                                <th style="width: 15%;">Departure</th>
                                <th style="width: 15%;">Arrival</th>
                                <th style="width: 15%;">Departure</th>
                                <th style="width: 15%;">Hours</th>
                                <th style="width: 15%;">Minutes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($emp['daily_logs'] as $log)
                                <tr>
                                    <td class="font-bold">{{ $log['day'] }}</td>
                                    
                                    {{-- 1. Check if Approved Leave --}}
                                    @if(isset($log['is_leave']) && $log['is_leave'])
                                        <td colspan="6" class="font-bold uppercase" style="font-size: 7px; letter-spacing: 1px;">
                                            ON LEAVE ({{ $log['leave_type'] }})
                                        </td>

                                    {{-- 2. Weekend: Spans across all 6 columns, no undertime --}}
                                    @elseif($log['is_weekend'])
                                        <td colspan="6" class="font-bold uppercase" style="letter-spacing: 2px;">
                                            {{ $log['label'] }}
                                        </td>
                                    
                                    {{-- 3. Weekday: Displays time entries AND undertime cells --}}
                                    @else
                                        <td>{{ $log['morning_in'] }}</td>
                                        <td>{{ $log['morning_out'] }}</td>
                                        <td>{{ $log['afternoon_in'] }}</td>
                                        <td>{{ $log['afternoon_out'] }}</td>
                                        <td>{{ $log['undertime_hours'] ?? '' }}</td>
                                        <td>{{ $log['undertime_minutes'] ?? '' }}</td>
                                    @endif
                                </tr>
                            @endforeach
                            <tr class="font-bold">
                                <td colspan="5" class="text-right" style="padding-right: 5px;">TOTAL</td>
                                <td>{{ $emp['total_undertime_hours'] }}</td>
                                <td>{{ $emp['total_undertime_minutes'] }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="margin-top: 8px; text-align: justify; font-size: 7px;">
                        I CERTIFY on my honor that the above is a true and correct report of hours of work performed, record of which was made daily at the time of arrival and departure from office.
                    </div>

                    <div class="text-center font-bold uppercase" style="margin-top: 20px;">
                        <span class="line-underline" style="width: 80%;">{{ $emp['name'] }}</span>
                    </div>

                    <div style="margin-top: 12px; font-size: 7px;">VERIFIED as to the prescribed office hours:</div>

                    <div class="text-center" style="margin-top: 20px;">
                        <span class="line-underline" style="width: 70%;"></span><br>
                        <span style="font-size: 7px;">In-Charge</span>
                    </div>
                </div>
            @endfor
            <div class="clear"></div>
        </div>
    @endforeach
</body>
</html>