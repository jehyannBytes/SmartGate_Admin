<?php
// app/Http/Controllers/DashboardController.php
// SmartGate ACC — Dashboard Overview Controller

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\AttendanceLog;
use App\Models\AttRecord;
use App\Models\LocatorSlip;
use App\Models\LeaveRequest;
use App\Models\Device;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        if ($user->isHR()) {
            return $this->hrDashboard($today);
        }

        return $this->ictmoDashboard($today);
    }

    // ── HR Dashboard ───────────────────────────────────────────────────
    private function hrDashboard(Carbon $today)
    {
        $data = [
            // Attendance summary today
            'total_logs_today'     => AttendanceLog::whereDate('scanned_at', $today)->count(),
            'morning_in_today'     => AttendanceLog::whereDate('scanned_at', $today)
                                        ->where('log_type', 'Morning In')->count(),
            'afternoon_in_today'   => AttendanceLog::whereDate('scanned_at', $today)
                                        ->where('log_type', 'Afternoon In')->count(),

            // Total employees
            'total_employees'      => Employee::where('is_active', true)->count(),
            'faculty_count'        => Employee::where('is_active', true)
                                        ->where('employment_type', 'faculty')->count(),
            'non_teaching_count'   => Employee::where('is_active', true)
                                        ->where('employment_type', 'non-teaching')->count(),

            // Pending approvals
            'pending_att'          => AttRecord::where('status', 'pending')->count(),
            'pending_locator'      => LocatorSlip::where('status', 'pending')->count(),
            'pending_leave'        => LeaveRequest::where('status', 'pending')->count(),

            // Recent attendance logs (latest 8)
            'recent_logs'          => AttendanceLog::with('employee')
                                        ->whereDate('scanned_at', $today)
                                        ->latest('scanned_at')
                                        ->limit(8)
                                        ->get(),

            // Top 5 late employees this month
            'late_employees'       => AttendanceLog::selectRaw('
                                            employee_id,
                                            COUNT(*) as late_count
                                        ')
                                        ->where('log_type', 'Morning In')
                                        ->whereMonth('scanned_at', $today->month)
                                        ->whereTime('scanned_at', '>', '08:00:00')
                                        ->groupBy('employee_id')
                                        ->orderByDesc('late_count')
                                        ->limit(5)
                                        ->with('employee')
                                        ->get(),
        ];

        return view('dashboard.hr', $data);
    }

    // ── ICTMO Dashboard ────────────────────────────────────────────────
    private function ictmoDashboard(Carbon $today)
    {
        $data = [
            // Employee stats
            'total_employees'      => Employee::where('is_active', true)->count(),
            'faculty_count'        => Employee::where('is_active', true)
                                        ->where('employment_type', 'faculty')->count(),
            'non_teaching_count'   => Employee::where('is_active', true)
                                        ->where('employment_type', 'non-teaching')->count(),
            'inactive_employees'   => Employee::where('is_active', false)->count(),

            // Device stats
            'total_devices'        => Device::count(),
            'active_devices'       => Device::where('is_active', true)->count(),
            'offline_devices'      => Device::where('is_active', true)
                                        ->where('last_seen_at', '<',
                                            Carbon::now()->subMinutes(10))
                                        ->count(),

            // Sync stats
            'pending_sync'         => AttendanceLog::where('sync_status', 'pending')->count(),
            'synced_today'         => AttendanceLog::whereDate('synced_at', $today)
                                        ->where('sync_status', 'synced')->count(),

            // Attendance today
            'total_logs_today'     => AttendanceLog::whereDate('scanned_at', $today)->count(),
            'unverified_logs'      => AttendanceLog::whereDate('scanned_at', $today)
                                        ->where('face_verified', false)->count(),

            // Unread notifications
            'unread_notifs'        => Auth::user()->notifications()
                                        ->where('is_read', false)->count(),

            // Recent devices activity
            'devices'              => Device::orderByDesc('last_seen_at')
                                        ->limit(5)->get(),

            // Recent unverified logs
            'unverified_recent'    => AttendanceLog::with('employee')
                                        ->whereDate('scanned_at', $today)
                                        ->where('face_verified', false)
                                        ->latest('scanned_at')
                                        ->limit(5)
                                        ->get(),
        ];

        return view('dashboard.ictmo', $data);
    }
}