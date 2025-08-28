<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ManageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Attendance Routes
|--------------------------------------------------------------------------
*/

Route::prefix('attendance')->group(function () {
    // Main attendance dashboard
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');

    // QR Code Generator
    Route::get('/generate-qr', [AttendanceController::class, 'generateQR'])->name('attendance.generate-qr');

    // Scan form (what QR code points to)
    Route::get('/scan', [AttendanceController::class, 'showScanForm'])->name('attendance.scan');
    Route::post('/scan', [AttendanceController::class, 'processScan'])->name('attendance.process-scan');

    // View attendance records
    Route::get('/records', [AttendanceController::class, 'records'])->name('attendance.records');

    // API endpoint for mobile/ajax scanning
    Route::post('/api/scan', [AttendanceController::class, 'apiScan'])->name('attendance.api-scan');
});
// Route::middleware(['auth'])->group(function () {
    Route::get('/agenda', [AgendaController::class, 'agenda'])->name('agenda.index');

    Route::get('/manage', [ManageController::class, 'manage'])->name('agenda.manage');
    Route::post('/manage', [ManageController::class, 'store'])->name('store');
    Route::get('/{meeting}/edit', [ManageController::class, 'edit'])->name('edit');
    Route::put('/{meeting}', [ManageController::class, 'update'])->name('update');
    Route::delete('/{meeting}', [ManageController::class, 'destroy'])->name('destroy');
    Route::patch('/{meeting}/status', [ManageController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/{meeting}/download/{documentIndex}', [ManageController::class, 'downloadDocument'])->name('downloadDocument');
// });
