<?php
// app/Http/Controllers/AnalyticsController.php
// SmartGate ACC — Analytics Dashboard (HR + ICTMO)

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $days  = (int) $request->input('days', 30);
        $start = now()->subDays($days - 1)->startOfDay();
        $end   = now()->endOfDay();

        $dailyLogs = AttendanceLog::where('log_type', 'Morning In')
            ->whereBetween('scanned_at', [$start, $end])
            ->get()
            ->groupBy(fn ($log) => Carbon::parse($log->scanned_at)->format('Y-m-d'));

        $trendLabels = [];
        $trendData   = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $trendLabels[] = $d->format('M d');
            $trendData[]   = $dailyLogs->get($key, collect())->count();
        }

        $departments = Employee::where('is_active', true)
            ->distinct()->pluck('department')->sort()->values();

        $lateByDept = [];
        foreach ($departments as $dept) {
            $employeeIds = Employee::where('department', $dept)
                ->where('is_active', true)
                ->pluck('employee_id');

            $lateCount = AttendanceLog::where('log_type', 'Morning In')
                ->whereIn('employee_id', $employeeIds)
                ->whereBetween('scanned_at', [$start, $end])
                ->get()
                ->filter(fn ($log) => Carbon::parse($log->scanned_at)->format('H:i') > '08:00')
                ->count();

            $lateByDept[$dept] = $lateCount;
        }

        $devices = Device::where('is_active', true)->get()->map(function ($device) {
            $device->is_online = $device->last_seen_at &&
                Carbon::parse($device->last_seen_at)->diffInMinutes(now()) < 10;
            return $device;
        });

        $deviceOnline  = $devices->where('is_online', true)->count();
        $deviceOffline = $devices->where('is_online', false)->count();

        $totalEmployees = Employee::where('is_active', true)->count();
        $totalLogsRange = AttendanceLog::whereBetween('scanned_at', [$start, $end])->count();
        $avgDailyIn     = $days > 0 ? round(array_sum($trendData) / $days, 1) : 0;
        $totalLateRange = array_sum($lateByDept);

        $logTypeBreakdown = AttendanceLog::whereBetween('scanned_at', [$start, $end])
            ->get()
            ->groupBy('log_type')
            ->map->count();

        return view('analytics.index', compact(
            'days',
            'trendLabels', 'trendData',
            'lateByDept',
            'deviceOnline', 'deviceOffline',
            'totalEmployees', 'totalLogsRange', 'avgDailyIn', 'totalLateRange',
            'logTypeBreakdown'
        ));
    }
}