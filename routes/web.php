<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\PublishController;
use App\Http\Controllers\RecordingController;
use App\Http\Controllers\EncodingController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Dom\Document;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


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
   
});
// Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/manage', [ManageController::class, 'manage'])->name('agenda.manage');
    Route::post('/manage', [ManageController::class, 'store'])->name('store');
    Route::get('/{meeting}/edit', [ManageController::class, 'edit'])->name('edit');
    Route::put('/{meeting}', [ManageController::class, 'update'])->name('update');
    Route::post('/meetings/{meeting}/approve', [ManageController::class, 'approve'])->name('meetings.approve');
    Route::post('/meetings/{meeting}/reject', [ManageController::class, 'reject'])->name('meetings.reject');
    Route::delete('/meetings/{meeting}', [ManageController::class, 'destroy'])->name('meetings.destroy');
    Route::patch('/{meeting}/status', [ManageController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/{meeting}/download/{documentIndex}', [ManageController::class, 'downloadDocument'])->name('downloadDocument');


    Route::get('/status', [PublishController::class, 'status'])->name('minutes.status');
    Route::post('/{meeting}/publish', [PublishController::class, 'publish'])->name('meetings.publish');
    Route::post('/{meeting}/unpublish', [PublishController::class, 'unpublish'])->name('meetings.unpublish');
    Route::get('/{id}/print', [PublishController::class, 'printMeeting'])->name('meetings.print');
    Route::get('/', [PublishController::class, 'landingPage'])->name('landing.page');
    

   
    Route::get('/recording', [RecordingController::class, 'index'])->name('recording');
     Route::get('/search', [RecordingController::class, 'searchMeetings'])->name('search');
    Route::get('/meeting/{id}', [RecordingController::class, 'getMeetingDetails'])->name('meeting.details');
    
    // Meeting control actions
    Route::post('/meeting/{id}/start', [RecordingController::class, 'startMeeting'])->name('meeting.start');
    Route::post('/meeting/{id}/complete', [RecordingController::class, 'completeMeeting'])->name('meeting.complete');
    Route::post('/meeting/{id}/save-progress', [RecordingController::class, 'saveProgress'])->name('meeting.save-progress');

    
    // Reports
    Route::get('/meeting/{id}/report', [RecordingController::class, 'generateReport'])->name('meeting.report');  

    Route::get('/encoding', [EncodingController::class, 'encoding'])->name('records.encoding');


    Route::get('/documents', [DocumentController::class, 'document'])->name('documents');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
// });

    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

