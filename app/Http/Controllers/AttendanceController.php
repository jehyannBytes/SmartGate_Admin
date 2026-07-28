<?php
// app/Http/Controllers/AttendanceController.php
// SmartGate ACC — Attendance Log View (HR and ICTMO)

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = AttendanceLog::with('employee');

        // ── Date Filter ───────────────────────────────────────────────
        $filter = $request->get('filter', 'today');
        $today  = Carbon::today();

        switch ($filter) {
            case 'week':
                $query->whereBetween('scanned_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;
            case 'month':
                $query->whereMonth('scanned_at', $today->month)
                      ->whereYear('scanned_at', $today->year);
                break;
            case 'custom':
                if ($request->filled('date_from')) {
                    $query->whereDate('scanned_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('scanned_at', '<=', $request->date_to);
                }
                break;
            default: // today
                $query->whereDate('scanned_at', $today);
        }

        // ── Search ────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%");
            });
        }

        // ── Log Type Filter ───────────────────────────────────────────
        if ($request->filled('log_type')) {
            $query->where('log_type', $request->log_type);
        }

        // ── Sync Status Filter ────────────────────────────────────────
        if ($request->filled('sync_status')) {
            $query->where('sync_status', $request->sync_status);
        }

        // ── Face Verified Filter ──────────────────────────────────────
        if ($request->filled('face_verified')) {
            $query->where('face_verified', $request->face_verified === '1');
        }

        $logs = $query->latest('scanned_at')->paginate(20)->withQueryString();

        // ── Summary counts ────────────────────────────────────────────
        $baseQuery = AttendanceLog::query();
        if ($filter === 'today') {
            $baseQuery->whereDate('scanned_at', $today);
        }

        $summary = [
            'total'         => (clone $baseQuery)->count(),
            'morning_in'    => (clone $baseQuery)->where('log_type', 'Morning In')->count(),
            'morning_out'   => (clone $baseQuery)->where('log_type', 'Morning Out')->count(),
            'afternoon_in'  => (clone $baseQuery)->where('log_type', 'Afternoon In')->count(),
            'afternoon_out' => (clone $baseQuery)->where('log_type', 'Afternoon Out')->count(),
            'unverified'    => (clone $baseQuery)->where('face_verified', false)->count(),
            'pending_sync'  => (clone $baseQuery)->where('sync_status', 'pending')->count(),
        ];

        return view('attendance.index', compact('logs', 'summary', 'filter'));
    }

    public function show(AttendanceLog $log): View
    {
        $log->load('employee', 'device');
        return view('attendance.show', compact('log'));
    }

    // ── API: Sync from gate client ────────────────────────────────────
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'employee_id'     => ['required', 'integer'],
            'device_id'       => ['nullable', 'integer'],
            'log_type'        => ['required', 'in:Morning In,Morning Out,Afternoon In,Afternoon Out'],
            'scanned_at'      => ['required', 'date'],
            'face_verified'   => ['boolean'],
            'geofence_passed' => ['boolean'],
            'local_uuid'      => ['required', 'string', 'unique:attendance_logs,local_uuid'],
        ]);

        $validated['sync_status'] = 'synced';
        $validated['synced_at']   = now();

        AttendanceLog::create($validated);

        return response()->json(['status' => 'synced'], 201);
    }
}
