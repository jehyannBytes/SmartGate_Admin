<?php
namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Employee;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeviceEmployeeController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_token'   => ['required', 'string'],
            'device_mac' => ['required', 'string'],
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

        if (! $employee->photo_url) {
            return response()->json([
                'success' => false,
                'error'   => 'no_reference_photo',
                'message' => 'This employee has no enrolled photo for face verification. Please contact ICTMO.',
            ], 422);
        }

        $device->update(['last_seen_at' => now()]);

        return response()->json([
            'success'  => true,
            'employee' => [
                'employee_id'     => $employee->employee_id,
                'employee_code'   => $employee->employee_code,
                'name'            => $employee->last_name . ', ' . $employee->first_name,
                'position'        => $employee->position,
                'department'      => $employee->department,
                'employment_type' => $employee->employment_type,
                'reference_photo_url' => asset($employee->photo_url),
            ],
        ], 200);
    }
}
