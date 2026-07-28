<?php
namespace App\Http\Controllers;

use App\Models\MonthlyReport;
use App\Models\AttendanceLog;
use App\Models\Employee;
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
        $year       = $report->year;
        $month      = $report->month;
        $department = $report->department !== 'All Departments' ? $report->department : null;

        $employeeQuery = Employee::where('is_active', true);
        if ($department) {
            $employeeQuery->where('department', $department);
        }
        $employees = $employeeQuery->orderBy('last_name')->get();

        $reportData = [];
        foreach ($employees as $employee) {
            $logs = AttendanceLog::where('employee_id', $employee->employee_id)
                ->whereYear('scanned_at', $year)
                ->whereMonth('scanned_at', $month)
                ->orderBy('scanned_at')
                ->get();

            $daysPresent = $logs->where('log_type', 'Morning In')->count();
            $lates = $logs->where('log_type', 'Morning In')
                ->filter(fn($l) => Carbon::parse($l->scanned_at)->format('H:i') > '08:00')
                ->count();

            $dailyLogs = $logs->groupBy(fn($l) => Carbon::parse($l->scanned_at)->format('Y-m-d'))
                ->map(function ($dayLogs, $date) {
                    $find = fn($type) => $dayLogs->firstWhere('log_type', $type);

                    $morningIn    = $find('Morning In');
                    $morningOut   = $find('Morning Out');
                    $afternoonIn  = $find('Afternoon In');
                    $afternoonOut = $find('Afternoon Out');

                    return [
                        'date'          => Carbon::parse($date)->format('M d, Y (D)'),
                        'morning_in'    => $morningIn ? Carbon::parse($morningIn->scanned_at)->format('h:i A') : '-',
                        'morning_out'   => $morningOut ? Carbon::parse($morningOut->scanned_at)->format('h:i A') : '-',
                        'afternoon_in'  => $afternoonIn ? Carbon::parse($afternoonIn->scanned_at)->format('h:i A') : '-',
                        'afternoon_out' => $afternoonOut ? Carbon::parse($afternoonOut->scanned_at)->format('h:i A') : '-',
                        'is_late'       => $morningIn && Carbon::parse($morningIn->scanned_at)->format('H:i') > '08:00',
                    ];
                })
                ->values();

            $reportData[] = [
                'employee_code'   => $employee->employee_code,
                'name'            => $employee->last_name . ', ' . $employee->first_name . ' ' . ($employee->middle_name ? substr($employee->middle_name, 0, 1).'.' : ''),
                'position'        => $employee->position,
                'department'      => $employee->department,
                'employment_type' => $employee->employment_type,
                'days_present'    => $daysPresent,
                'lates'           => $lates,
                'morning_in'      => $logs->where('log_type', 'Morning In')->count(),
                'morning_out'     => $logs->where('log_type', 'Morning Out')->count(),
                'afternoon_in'    => $logs->where('log_type', 'Afternoon In')->count(),
                'afternoon_out'   => $logs->where('log_type', 'Afternoon Out')->count(),
                'daily_logs'      => $dailyLogs,
            ];
        }

        $monthName = Carbon::create($year, $month)->format('F Y');

        return [$reportData, $monthName];
    }
}