<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\QrCode;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::where('is_active', true)
                    ->with(['qrCodes' => function ($q) {
                        $q->latest('issued_at')->limit(1);
                    }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('employee_code', 'like', "%$search%");
            });
        }

        if ($request->filled('qr_status')) {
            if ($request->qr_status === 'issued') {
                $query->whereHas('qrCodes', fn ($q) => $q->where('is_active', true));
            } elseif ($request->qr_status === 'none') {
                $query->whereDoesntHave('qrCodes', fn ($q) => $q->where('is_active', true));
            }
        }

        $employees = $query->orderBy('last_name')->paginate(15)->withQueryString();

        $totalEmployees = Employee::where('is_active', true)->count();
        $issuedCount    = QrCode::where('is_active', true)->count();
        $noQrCount      = $totalEmployees - Employee::where('is_active', true)
                            ->whereHas('qrCodes', fn ($q) => $q->where('is_active', true))
                            ->count();

        return view('qr-codes.index', compact(
            'employees', 'totalEmployees', 'issuedCount', 'noQrCount'
        ));
    }

    public function show(Employee $employee): View
    {
        $activeQr = $employee->qrCodes()
                        ->where('is_active', true)
                        ->latest('issued_at')
                        ->first();

        $history = $employee->qrCodes()
                        ->latest('issued_at')
                        ->limit(10)
                        ->get();

        $qrImage = null;
        if ($activeQr) {
            $svg = QrCodeGenerator::size(220)->generate($activeQr->qr_token);
            $qrImage = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }

        return view('qr-codes.show', compact('employee', 'activeQr', 'history', 'qrImage'));
    }

    public function generate(Employee $employee): RedirectResponse
    {
        QrCode::where('employee_id', $employee->employee_id)
              ->where('is_active', true)
              ->update(['is_active' => false]);

        $token = $this->generateUniqueToken($employee);

        QrCode::create([
            'employee_id' => $employee->employee_id,
            'qr_token'    => $token,
            'issued_at'   => now(),
            'expires_at'  => null,
            'is_active'   => true,
        ]);

        AuditLogger::log('created', $employee, "Generated QR code for {$employee->last_name}, {$employee->first_name}");

        return redirect()->route('qr-codes.show', $employee)
                         ->with('success', 'QR code generated successfully.');
    }

    public function regenerate(Employee $employee): RedirectResponse
    {
        $this->generate($employee);

        AuditLogger::log('regenerated', $employee, "Regenerated QR code for {$employee->last_name}, {$employee->first_name}");

        return redirect()->route('qr-codes.show', $employee)
                         ->with('success', 'QR code regenerated. The previous code is now invalid.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $updated = QrCode::where('employee_id', $employee->employee_id)
                        ->where('is_active', true)
                        ->update(['is_active' => false]);

        $message = $updated
            ? 'QR code revoked successfully.'
            : 'This employee has no active QR code to revoke.';

        if ($updated) {
            AuditLogger::log('revoked', $employee, "Revoked QR code for {$employee->last_name}, {$employee->first_name}");
        }

        return redirect()->route('qr-codes.show', $employee)->with('success', $message);
    }

    private function generateUniqueToken(Employee $employee): string
    {
        do {
            $token = strtoupper($employee->employee_code) . '-' . Str::random(24);
        } while (QrCode::where('qr_token', $token)->exists());

        return $token;
    }
}