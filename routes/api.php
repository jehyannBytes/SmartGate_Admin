<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\AttendanceSyncController;

Route::middleware('device.token')->group(function () {
    Route::prefix('device')->group(function () {
        Route::get('/employees', [DeviceController::class, 'employees']);
    });

    Route::post('/attendance/sync', [AttendanceSyncController::class, 'sync']);
});

