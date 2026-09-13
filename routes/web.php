<?php
// routes/web.php
// SmartGate ACC Admin Dashboard Web Routes

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttRecordController;
use App\Http\Controllers\LocatorSlipController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FaceEnrollmentController;

// Root redirect 
Route::get('/', fn () => redirect()->route('login'));


// GUEST ROUTES (not logged in)

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
         ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});


// AUTHENTICATED ROUTES (must be logged in)

Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');

    // Both HR and ICTMO 
    Route::middleware('role:hr,ictmo')->group(function () {

        // Dashboard redirects to hr or ictmo view based on role
        Route::get('/dashboard', [DashboardController::class, 'index'])
             ->name('dashboard');

        // Attendance logs view only for both roles
        Route::get('/attendance', [AttendanceController::class, 'index'])
             ->name('attendance.index');
        Route::get('/attendance/{log}', [AttendanceController::class, 'show'])
             ->name('attendance.show');

        // Audit trail
        Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])
             ->name('audit-logs.index');

        // Analytics dashboard
        Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])
             ->name('analytics.index');
    });

    // HR ONLY 
    Route::middleware('role:hr')->group(function () {

        // ATT Records
        Route::get('/att-records', [AttRecordController::class, 'index'])
             ->name('att-records.index');
        Route::get('/att-records/create', [AttRecordController::class, 'create'])
             ->name('att-records.create');
        Route::post('/att-records', [AttRecordController::class, 'store'])
             ->name('att-records.store');
        Route::get('/att-records/{attRecord}', [AttRecordController::class, 'show'])
             ->name('att-records.show');
        Route::patch('/att-records/{attRecord}', [AttRecordController::class, 'update'])
             ->name('att-records.update');

        // Locator Slips
        Route::get('/locator-slips', [LocatorSlipController::class, 'index'])
             ->name('locator-slips.index');
        Route::get('/locator-slips/{locatorSlip}', [LocatorSlipController::class, 'show'])
             ->name('locator-slips.show');
        Route::patch('/locator-slips/{locatorSlip}', [LocatorSlipController::class, 'update'])
             ->name('locator-slips.update');

        // Leave Requests
        Route::get('/leave-requests', [LeaveRequestController::class, 'index'])
             ->name('leave-requests.index');
        Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])
             ->name('leave-requests.create');
        Route::post('/leave-requests', [LeaveRequestController::class, 'store'])
             ->name('leave-requests.store');
        Route::get('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'show'])
             ->name('leave-requests.show');
        Route::patch('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'update'])
             ->name('leave-requests.update');

        // Monthly Reports
        Route::get('/reports', [MonthlyReportController::class, 'index'])
             ->name('reports.index');
        Route::post('/reports/generate', [MonthlyReportController::class, 'generate'])
             ->name('reports.generate');
        Route::get('/reports/{report}/download', [MonthlyReportController::class, 'download'])
             ->name('reports.download');
        Route::get('/reports/{report}/export-pdf', [MonthlyReportController::class, 'exportPdf'])
             ->name('reports.export-pdf');
        Route::get('/reports/{report}/export-csv', [MonthlyReportController::class, 'exportCsv'])
             ->name('reports.export-csv');
        Route::delete('/reports/{report}', [MonthlyReportController::class, 'destroy'])
             ->name('reports.destroy');
    
    });

    // ICTMO ONLY 
    Route::middleware('role:ictmo')->group(function () {

        // Employee management — full CRUD
        Route::resource('employees', EmployeeController::class);

        // Face enrollment (live webcam capture)
        Route::get('/employees/{employee}/enroll-face', [FaceEnrollmentController::class, 'show'])
             ->name('employees.enroll-face');
        Route::post('/employees/{employee}/enroll-face', [FaceEnrollmentController::class, 'storeCapture'])
             ->name('employees.enroll-face.store');
        Route::delete('/employees/{employee}/enroll-face/{capture}', [FaceEnrollmentController::class, 'destroyCapture'])
             ->name('employees.enroll-face.destroy');
        Route::post('/employees/{employee}/enroll-face/complete', [FaceEnrollmentController::class, 'complete'])
             ->name('employees.enroll-face.complete');

        // Bulk employee import
        Route::get('/employees-import', [\App\Http\Controllers\EmployeeImportController::class, 'create'])
             ->name('employees.import');
        Route::get('/employees-import/template', [\App\Http\Controllers\EmployeeImportController::class, 'template'])
             ->name('employees.import.template');
        Route::post('/employees-import', [\App\Http\Controllers\EmployeeImportController::class, 'store'])
             ->name('employees.import.store');

        // Device management — full CRUD
        Route::resource('devices', DeviceController::class);

        // Employee ID Card generation
        Route::get('/id-cards/{employee}', [\App\Http\Controllers\IdCardController::class, 'show'])
             ->name('id-cards.show');
        Route::get('/id-cards/{employee}/download', [\App\Http\Controllers\IdCardController::class, 'download'])
             ->name('id-cards.download');

        Route::get('/qr-codes', [\App\Http\Controllers\QrCodeController::class, 'index'])
             ->name('qr-codes.index');
        Route::get('/qr-codes/{employee}', [\App\Http\Controllers\QrCodeController::class, 'show'])
             ->name('qr-codes.show');
        Route::post('/qr-codes/{employee}/generate', [\App\Http\Controllers\QrCodeController::class, 'generate'])
             ->name('qr-codes.generate');
        Route::post('/qr-codes/{employee}/regenerate', [\App\Http\Controllers\QrCodeController::class, 'regenerate'])
             ->name('qr-codes.regenerate');
        Route::delete('/qr-codes/{employee}', [\App\Http\Controllers\QrCodeController::class, 'destroy'])
             ->name('qr-codes.destroy');

        // System notifications
        Route::get('/notifications', [NotificationController::class, 'index'])
             ->name('notifications.index');
        Route::patch('/notifications/{notification}/read',
             [NotificationController::class, 'markRead'])
             ->name('notifications.read');
        Route::patch('/notifications/read-all',
             [NotificationController::class, 'markAllRead'])
             ->name('notifications.read-all');
    });

});


// DEVICE / GATE CLIENT API ROUTES (no login session device-based auth)

Route::middleware('device.token')->group(function () {
    Route::post('/api/attendance/verify-qr', [\App\Http\Controllers\AttendanceScanController::class, 'verify'])
         ->name('attendance.verify-qr');

    Route::post('/api/device/employee-lookup', [\App\Http\Controllers\DeviceEmployeeController::class, 'lookup'])
         ->name('device.employee-lookup');
});