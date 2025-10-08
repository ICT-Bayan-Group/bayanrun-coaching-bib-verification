<?php
use App\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Event Lari Routes - Public Form
Route::get('/', [PublicFormController::class, 'index'])->name('event.form');

// Email Verification Route
Route::post('/verify-email', [PublicFormController::class, 'verifyEmail'])->name('event.verify.email');

// Registration Route
Route::post('/daftar', [PublicFormController::class, 'store'])->name('event.store');

// FIXED: Add the route that JavaScript is calling
Route::get('/check-registration-status', [PublicFormController::class, 'checkRegistrationStatus'])->name('check.registration.status');

// QR Code Verification Routes - UPDATED
Route::get('/verify', [PublicFormController::class, 'verifyQR'])->name('qr.verify.email');
Route::post('/verify', [PublicFormController::class, 'verifyQR'])->name('qr.verify.email.post');

// WhatsApp Test Route
Route::get('/test-whatsapp', [PublicFormController::class, 'testWhatsApp'])->name('test.whatsapp');
Route::post('/test-whatsapp', [PublicFormController::class, 'testWhatsApp'])->name('test.whatsapp.post');

// QR Code Retry Route
Route::post('/retry-qr/{pesertaId}', [PublicFormController::class, 'retryQRCode'])->name('retry.qr');

// REMOVED: Duplicate route (moved above)
// Route::get('/registration-status', [PublicFormController::class, 'checkRegistrationStatus'])->name('registration.status');

// Admin Routes - Dashboard & Management
//Route::prefix('admin')->name('admin.')->group(function () {
 //   Route::get('/', [PublicFormController::class, 'showRegistrations'])->name('dashboard');
 //   Route::get('/dashboard', [PublicFormController::class, 'showRegistrations'])->name('registrations.index');
  //  Route::get('/peserta', [PublicFormController::class, 'showRegistrations'])->name('peserta.index');
  //  Route::get('/peserta/{id}', [PublicFormController::class, 'showDetail'])->name('peserta.detail');
   // Route::get('/export', [PublicFormController::class, 'export'])->name('export');
    
    // Regenerate QR Route
    Route::post('/peserta/{id}/regenerate-qr', [PublicFormController::class, 'regenerateQR'])->name('regenerate.qr');
    
    // Bulk Operations
    Route::post('/bulk-send-qr', [PublicFormController::class, 'bulkSendQR'])->name('bulk.send.qr');
    
    // Registration Limit Management
    Route::get('/registration-limit', [PublicFormController::class, 'getRegistrationLimit'])->name('registration.limit');
    
    // Admin-specific registration status check
    Route::get('/registration-status', [PublicFormController::class, 'checkRegistrationStatus'])->name('registration.status');
});

// API Routes - Statistics & QR Verification
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/stats', [PublicFormController::class, 'getStats'])->name('stats');
    Route::get('/registration-status', [PublicFormController::class, 'checkRegistrationStatus'])->name('registration.status');
    
    // QR Verification API
    Route::get('/verify/{token}', [PublicFormController::class, 'verifyQR'])->name('qr.verify.token');
    Route::post('/verify', [PublicFormController::class, 'verifyQR'])->name('qr.verify.api');
});

// Legacy route for backward compatibility
Route::get('/verify/{token}', [PublicFormController::class, 'verifyQR'])->name('qr.verify.short');

// ADDED: Missing routes that might be needed based on common patterns
Route::get('/peserta/{id}/qr-code', [PublicFormController::class, 'showQRCode'])->name('peserta.qr');
Route::get('/download-qr/{id}', [PublicFormController::class, 'downloadQR'])->name('download.qr');