<?php
namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\Employee;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceScanController extends Controller
{
    private const MIN_SCAN_GAP_MINUTES = 60;

    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_token'        => ['required', 'string'],
            'device_mac'      => ['required', 'string'],
            'geofence_passed' => ['boolean'],
            'face_verified'   => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => 'validation_failed',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $data = $validator->validated();

        $device = Device::where('device_mac', $data['device_mac'])
                        ->where('is_active', true)
                        ->first();

        if (! $device) {
            return response()->json([
                'success' => false,
                'error'   => 'unknown_device',
                'message' => 'This device is not registered or has been deactivated.',
            ], 403);
        }

        $qrCode = QrCode::where('qr_token', $data['qr_token'])
                        ->where('is_active', true)
                        ->first();

        if (! $qrCode) {
            return response()->json([
                'success' => false,
                'error'   => 'invalid_qr',
                'message' => 'QR code is invalid, revoked, or has been replaced.',
            ], 404);
        }

        $employee = Employee::where('employee_id', $qrCode->employee_id)
                        ->where('is_active', true)
                        ->first();

        if (! $employee) {
            return response()->json([
                'success' => false,
                'error'   => 'employee_inactive',
                'message' => 'This employee record is inactive.',
            ], 403);
        }

        if (! $data['face_verified']) {
            return response()->json([
                'success' => false,
                'error'   => 'face_not_verified',
                'message' => 'Face verification failed. Scan rejected.',
            ], 401);
        }

        $logType = $this->determineLogType($employee->employee_id);

        if ($logType === null) {
            return response()->json([
                'success' => false,
                'error'   => 'attendance_complete',
                'message' => 'All 4 attendance scans for today are already complete for this employee.',
                'employee' => $this->employeeSummary($employee),
            ], 409);
        }

        if ($logType === 'too_soon') {
            return response()->json([
                'success' => false,
                'error'   => 'scan_too_soon',
                'message' => 'This QR was just scanned. Please wait at least ' . self::MIN_SCAN_GAP_MINUTES . ' minutes before scanning again.',
                'employee' => $this->employeeSummary($employee),
            ], 429);
        }

        $log = AttendanceLog::create([
            'employee_id'     => $employee->employee_id,
            'device_id'       => $device->device_id,
            'log_type'        => $logType,
            'scanned_at'      => now(),
            'geofence_passed' => $data['geofence_passed'] ?? false,
            'face_verified'   => true,
            'sync_status'     => 'synced',
            'local_uuid'      => (string) \Illuminate\Support\Str::uuid(),
            'synced_at'       => now(),
        ]);

        $device->update(['last_seen_at' => now()]);

        return response()->json([
            'success'  => true,
            'message'  => "{$logType} recorded successfully.",
            'log_type' => $logType,
            'scanned_at' => $log->scanned_at->toIso8601String(),
            'scan_number_today' => AttendanceLog::where('employee_id', $employee->employee_id)
                                        ->whereDate('scanned_at', now()->toDateString())
                                        ->count(),
            'employee' => $this->employeeSummary($employee),
        ], 200);
    }

    private function determineLogType(int $employeeId): ?string
    {
        $sequence = ['Morning In', 'Morning Out', 'Afternoon In', 'Afternoon Out'];

        $todayLogs = AttendanceLog::where('employee_id', $employeeId)
            ->whereDate('scanned_at', now()->toDateString())
            ->orderBy('scanned_at')
            ->get();

        if ($todayLogs->count() >= 4) {
            return null;
        }

        if ($todayLogs->isEmpty()) {
            return 'Morning In';
        }

        $lastLog = $todayLogs->last();
        $minutesSinceLastScan = Carbon::parse($lastLog->scanned_at)->diffInMinutes(now());

        if ($minutesSinceLastScan < self::MIN_SCAN_GAP_MINUTES) {
            return 'too_soon';
        }

        $nextIndex = $todayLogs->count();

        return $sequence[$nextIndex] ?? null;
    }

    private function employeeSummary(Employee $employee): array
    {
        return [
            'employee_id'   => $employee->employee_id,
            'employee_code' => $employee->employee_code,
            'name'          => $employee->last_name . ', ' . $employee->first_name,
            'department'    => $employee->department,
            'position'      => $employee->position,
            'photo_url'     => $employee->photo_url,
        ];
    }
}