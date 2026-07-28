<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\QrCode;
use App\Models\EmployeeFaceCapture;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    /**
     * Return the full active employee list + their QR codes + face
     * enrollment capture URLs. Called by the gate device (SmartGate app)
     * to sync its local offline database, including biometric templates.
     */
    public function employees(): JsonResponse
    {
        $employees = Employee::where('is_active', true)->get();
        $qrCodes = QrCode::where('is_active', true)->get();

        $faceCaptures = EmployeeFaceCapture::all()
            ->groupBy('employee_id')
            ->map(function ($captures) {
                return $captures->map(fn ($c) => asset($c->image_path))->values();
            });

        return response()->json([
            'success' => true,
            'employees' => $employees,
            'qr_codes' => $qrCodes,
            'face_captures' => $faceCaptures,
        ]);
    }
}