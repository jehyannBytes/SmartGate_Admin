<?php

namespace App\Http\Controllers;

use App\Models\MonthlyReport;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\AttRecord;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MonthlyReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports     = MonthlyReport::with('generatedBy')
                         ->latest('generated_at')
                         ->paginate(15);

        $departments = Employee::distinct()->pluck('department')->sort()->values();

        $currentYear  = now()->year;
        $currentMonth = now()->month;

        return view('reports.index', compact(
            'reports', 'departments', 'currentYear', 'currentMonth'
        ));
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'year'       => ['required', 'integer', 'min:2020', 'max:' . now()->year],
            'month'      => ['required', 'integer', 'min:1', 'max:12'],
            'department' => ['nullable', 'string'],
        ]);

        $year       = $request->year;
        $month      = $request->month;
        $department = $request->department;

        $report = MonthlyReport::create([
            'generated_by' => Auth::id(),
            'year'         => $year,
            'month'        => $month,
            'department'   => $department ?: 'All Departments',
            'generated_at' => now(),
            'file_url'     => null,
        ]);

        AuditLogger::log('created', $report, "Generated monthly report for " . Carbon::create($year, $month)->format('F Y'));

        return redirect()->route('reports.download', $report)
                         ->with('success', 'Report generated successfully.');
    }

    public function download(MonthlyReport $report): View
    {
        $report->load('generatedBy');

        [$reportData, $monthName] = $this->buildReportData($report);

        return view('reports.show', compact('report', 'reportData', 'monthName'));
    }

    public function exportPdf(MonthlyReport $report): Response
    {
        $report->load('generatedBy');
        [$reportData, $monthName] = $this->buildReportData($report);

        $pdf = Pdf::loadView('reports.pdf', compact('report', 'reportData', 'monthName'))
                   ->setPaper('legal', 'landscape');

        AuditLogger::log('exported', $report, "Exported monthly report ({$monthName}) as PDF");

        $filename = 'Monthly-Report-' . str_replace(' ', '-', $monthName) . '.pdf';

        return $pdf->download($filename);
    }

    public function exportCsv(MonthlyReport $report): Response
    {
        [$reportData, $monthName] = $this->buildReportData($report);

        $headers = [
            'Employee Code', 'Name', 'Position', 'Department', 'Employment Type',
            'Days Present', 'Lates', 'Morning In', 'Morning Out', 'Afternoon In', 'Afternoon Out',
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($reportData as $row) {
            $csv .= implode(',', [
                '"' . $row['employee_code'] . '"',
                '"' . str_replace('"', '""', $row['name']) . '"',
                '"' . str_replace('"', '""', $row['position']) . '"',
                '"' . str_replace('"', '""', $row['department']) . '"',
                '"' . $row['employment_type'] . '"',
                $row['days_present'],
                $row['lates'],
                $row['morning_in'],
                $row['morning_out'],
                $row['afternoon_in'],
                $row['afternoon_out'],
            ]) . "\n";
        }

        AuditLogger::log('exported', $report, "Exported monthly report ({$monthName}) as CSV");

        $filename = 'Monthly-Report-' . str_replace(' ', '-', $monthName) . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function destroy(MonthlyReport $report): RedirectResponse
    {
        $label = Carbon::create($report->year, $report->month)->format('F Y');
        AuditLogger::log('deleted', $report, "Deleted monthly report for {$label}");

        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Report deleted.');
    }

    private function buildReportData(MonthlyReport $report): array
    {
        $year = $report->year;
        $month = $report->month;
        $department = $report->department !== 'All Departments' ? $report->department : null;

        $employeeQuery = Employee::where('is_active', true);
        if ($department) {
            $employeeQuery->where('department', $department);
        }
        $employees = $employeeQuery->orderBy('last_name')->get();

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $reportData = [];

        foreach ($employees as $employee) {
            $logs = AttendanceLog::where('employee_id', $employee->employee_id)
                ->whereYear('scanned_at', $year)
                ->whereMonth('scanned_at', $month)
                ->get();

            // Fetch all approved ATT records for this employee overlapping this month
            $approvedAtts = AttRecord::where('employee_id', $employee->employee_id)
                ->where('status', 'approved')
                ->where(function ($q) use ($year, $month) {
                    $q->whereYear('departure_date', $year)->whereMonth('departure_date', $month)
                      ->orWhereYear('arrival_date', $year)->whereMonth('arrival_date', $month);
                })->get();

            $dailyLogs = [];
            $totalUndertimeHours = 0;
            $totalUndertimeMinutes = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dateObj = Carbon::create($year, $month, $day);
                $dayOfWeek = strtoupper($dateObj->format('l'));
                $currentDateStr = $dateObj->format('Y-m-d');

                if ($dateObj->isWeekend()) {
                    $dailyLogs[$day] = [
                        'day' => $day,
                        'is_weekend' => true,
                        'label' => $dayOfWeek,
                    ];
                    continue;
                }

                // Check for approved ATT record
                $activeAtt = $approvedAtts->first(function ($att) use ($currentDateStr) {
                    return $currentDateStr >= $att->departure_date->format('Y-m-d') 
                        && $currentDateStr <= $att->arrival_date->format('Y-m-d');
                });

                if ($activeAtt) {
                    $dailyLogs[$day] = [
                        'day' => $day,
                        'is_weekend' => false,
                        'is_att' => true,
                        'label' => 'ATT',
                        'att_number' => $activeAtt->att_number,
                        'document_url' => $activeAtt->image_url,
                        'morning_in' => 'ATT',
                        'morning_out' => 'ATT',
                        'afternoon_in' => 'ATT',
                        'afternoon_out' => 'ATT',
                        'undertime_hours' => '',
                        'undertime_minutes' => '',
                    ];
                    continue;
                }

                $dayLogs = $logs->filter(fn($l) => Carbon::parse($l->scanned_at)->day == $day);

                $mIn  = $dayLogs->firstWhere('log_type', 'Morning In');
                $mOut = $dayLogs->firstWhere('log_type', 'Morning Out');
                $aIn  = $dayLogs->firstWhere('log_type', 'Afternoon In');
                $aOut = $dayLogs->firstWhere('log_type', 'Afternoon Out');

                // Calculate undertime minutes (Arriving after 08:00 AM)
                $lateMinutes = 0;
                if ($mIn && Carbon::parse($mIn->scanned_at)->format('H:i') > '08:00') {
                    $lateMinutes = Carbon::parse($mIn->scanned_at)->diffInMinutes(Carbon::parse($dateObj->format('Y-m-d') . ' 08:00:00'));
                }

                $hours = floor($lateMinutes / 60);
                $mins = $lateMinutes % 60;

                $totalUndertimeHours += $hours;
                $totalUndertimeMinutes += $mins;

                $dailyLogs[$day] = [
                    'day' => $day,
                    'is_weekend' => false,
                    'is_att' => false,
                    'morning_in' => $mIn ? Carbon::parse($mIn->scanned_at)->format('h:i') : '',
                    'morning_out' => $mOut ? Carbon::parse($mOut->scanned_at)->format('h:i') : '',
                    'afternoon_in' => $aIn ? Carbon::parse($aIn->scanned_at)->format('h:i') : '',
                    'afternoon_out' => $aOut ? Carbon::parse($aOut->scanned_at)->format('h:i') : '',
                    'undertime_hours' => $hours > 0 ? $hours : '',
                    'undertime_minutes' => $mins > 0 ? $mins : '',
                ];
            }

            // Adjust overflow minutes to hours
            $totalUndertimeHours += floor($totalUndertimeMinutes / 60);
            $totalUndertimeMinutes = $totalUndertimeMinutes % 60;

            $reportData[] = [
                'employee_code' => $employee->employee_code,
                'name' => strtoupper($employee->first_name . ' ' . ($employee->middle_name ? substr($employee->middle_name, 0, 1) . '. ' : '') . $employee->last_name),
                'position' => $employee->position,
                'department' => $employee->department,
                'daily_logs' => $dailyLogs,
                'total_undertime_hours' => $totalUndertimeHours ?: '',
                'total_undertime_minutes' => $totalUndertimeMinutes ?: '',
            ];
        }

        $monthName = Carbon::create($year, $month)->format('F Y');

        return [$reportData, $monthName];
    }
}