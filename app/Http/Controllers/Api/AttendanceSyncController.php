<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AttendanceSyncController extends Controller
{
    /**
     * POST /api/attendance/sync
     *
     * Accepts a bulk array of attendance logs queued locally on the gate
     * device (offline-first). Each log carries a local_uuid generated on
     * the device, used here to prevent duplicate inserts on retry.
     */
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_mac'                    => ['required', 'string'],
            'logs'                           => ['required', 'array', 'min:1'],
            'logs.*.employee_id'             => ['required', 'integer'],
            'logs.*.log_type'                => ['required', 'string'],
            'logs.*.scanned_at'              => ['required', 'date'],
            'logs.*.geofence_passed'         => ['required', 'boolean'],
            'logs.*.face_verified'           => ['required', 'boolean'],
            'logs.*.local_uuid'              => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payload',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_mac', $request->input('device_mac'))->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown device. Please register this device first.',
            ], 404);
        }

        $incomingLogs = $request->input('logs');
        $results = [];

        DB::beginTransaction();
        try {
            foreach ($incomingLogs as $entry) {
                $existing = AttendanceLog::where('local_uuid', $entry['local_uuid'])->first();

                if ($existing) {
                    $results[] = [
                        'local_uuid' => $entry['local_uuid'],
                        'status'     => 'already_synced',
                        'log_id'     => $existing->log_id,
                    ];
                    continue;
                }

                $log = AttendanceLog::create([
                    'employee_id'      => $entry['employee_id'],
                    'device_id'        => $device->device_id,
                    'log_type'         => $entry['log_type'],
                    'scanned_at'       => $entry['scanned_at'],
                    'geofence_passed'  => $entry['geofence_passed'],
                    'face_verified'    => $entry['face_verified'],
                    'sync_status'      => 'synced',
                    'local_uuid'       => $entry['local_uuid'],
                    'synced_at'        => now(),
                ]);

                $results[] = [
                    'local_uuid' => $entry['local_uuid'],
                    'status'     => 'synced',
                    'log_id'     => $log->log_id,
                ];
            }

            $device->update(['last_seen_at' => now()]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Attendance sync failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sync failed, please retry.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => count($results) . ' log(s) processed.',
            'results' => $results,
        ], 200);
    }
}
