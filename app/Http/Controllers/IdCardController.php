<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class IdCardController extends Controller
{
    public function show(Employee $employee): View
    {
        $qrSource = $this->resolveQrSource($employee);
        return view('id-cards.show', compact('employee', 'qrSource'));
    }

    public function download(Employee $employee): Response
    {
        $qrSource  = $this->resolveQrSource($employee);
        $photoData = $this->resolveImageAsBase64(public_path($employee->photo_url ?? ''));
        $logoData  = $this->resolveImageAsBase64(public_path('images/logo.png'));

        $pdf = Pdf::loadView('id-cards.pdf', compact('employee', 'qrSource', 'photoData', 'logoData'))
                   ->setPaper([0, 0, 226.77, 368.50]); // portrait badge ~ 3in x 5.11in

        $filename = 'ID-' . $employee->employee_code . '.pdf';

        return $pdf->download($filename);
    }

    private function resolveQrSource(Employee $employee): ?string
    {
        $activeQr = $employee->qrCodes()->where('is_active', true)->latest('issued_at')->first();

        if (! $activeQr) {
            return null;
        }

        $svg = QrCodeGenerator::size(200)->generate($activeQr->qr_token);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private function resolveImageAsBase64(?string $absolutePath): ?string
    {
        if (! $absolutePath || ! file_exists($absolutePath)) {
            return null;
        }

        $type = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $data = file_get_contents($absolutePath);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}