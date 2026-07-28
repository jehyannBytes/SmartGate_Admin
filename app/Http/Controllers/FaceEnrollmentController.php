<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeFaceCapture;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FaceEnrollmentController extends Controller
{
    private const MIN_CAPTURES = 5;

    public function show(Employee $employee): View
    {
        $existingCount = EmployeeFaceCapture::where('employee_id', $employee->employee_id)->count();

        return view('employees.enroll-face', [
            'employee'      => $employee,
            'existingCount' => $existingCount,
            'minCaptures'   => self::MIN_CAPTURES,
        ]);
    }

    public function storeCapture(Request $request, Employee $employee)
    {
        $request->validate([
            'image' => ['required', 'string'],
        ]);

        $imageData = $request->input('image');

        if (!preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $imageData, $matches)) {
            return response()->json(['success' => false, 'message' => 'Invalid image format.'], 422);
        }

        $extension = $matches[1] === 'jpg' ? 'jpeg' : $matches[1];
        $base64 = substr($imageData, strpos($imageData, ',') + 1);
        $binary = base64_decode($base64, true);

        if ($binary === false) {
            return response()->json(['success' => false, 'message' => 'Could not decode image.'], 422);
        }

        if (strlen($binary) > 5 * 1024 * 1024) {
            return response()->json(['success' => false, 'message' => 'Image too large.'], 422);
        }

        $filename = 'face-captures/' . $employee->employee_id . '/' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($filename, $binary);

        $capture = EmployeeFaceCapture::create([
            'employee_id' => $employee->employee_id,
            'image_path'  => 'storage/' . $filename,
        ]);

        $totalCount = EmployeeFaceCapture::where('employee_id', $employee->employee_id)->count();

        return response()->json([
            'success'      => true,
            'capture_id'   => $capture->capture_id,
            'image_url'    => asset($capture->image_path),
            'total_count'  => $totalCount,
            'min_reached'  => $totalCount >= self::MIN_CAPTURES,
        ]);
    }

    public function destroyCapture(Employee $employee, EmployeeFaceCapture $capture)
    {
        if ($capture->employee_id !== $employee->employee_id) {
            abort(403);
        }

        $relativePath = str_replace('storage/', '', $capture->image_path);
        Storage::disk('public')->delete($relativePath);
        $capture->delete();

        $totalCount = EmployeeFaceCapture::where('employee_id', $employee->employee_id)->count();

        return response()->json([
            'success'     => true,
            'total_count' => $totalCount,
        ]);
    }

    public function complete(Request $request, Employee $employee): RedirectResponse
    {
        $totalCount = EmployeeFaceCapture::where('employee_id', $employee->employee_id)->count();

        if ($totalCount < self::MIN_CAPTURES) {
            return redirect()
                ->route('employees.enroll-face', $employee)
                ->with('error', 'Please capture at least ' . self::MIN_CAPTURES . ' photos before finishing.');
        }

        AuditLogger::log(
            'face_enrolled',
            $employee,
            "Enrolled {$totalCount} face captures for {$employee->last_name}, {$employee->first_name}"
        );

        return redirect()->route('employees.show', $employee)
                         ->with('success', 'Face enrollment complete. The gate device will sync the new templates automatically.');
    }
}