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

// BIB Verification Route
Route::post('/verify-bib', [PublicFormController::class, 'verifyBib'])->name('event.verify-bib');

// Registration Route
Route::post('/daftar', [PublicFormController::class, 'store'])->name('event.store');

// WhatsApp Test Route
Route::get('/test-whatsapp', [PublicFormController::class, 'testWhatsApp'])->name('test.whatsapp');
Route::post('/test-whatsapp', [PublicFormController::class, 'testWhatsApp'])->name('test.whatsapp.post');

// QR Code Retry Route - NEW
Route::post('/retry-qr/{pesertaId}', [PublicFormController::class, 'retryQRCode'])->name('retry.qr');

// Admin Routes - Dashboard & Management
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [PublicFormController::class, 'showRegistrations'])->name('registrations.index');
    Route::get('/peserta', [PublicFormController::class, 'showRegistrations'])->name('registrations.index');
    Route::get('/peserta/{id}', [PublicFormController::class, 'showDetail'])->name('registrations.detail');
    Route::get('/export', [PublicFormController::class, 'export'])->name('export');
    
    // Regenerate QR Route
    Route::post('/peserta/{id}/regenerate-qr', [PublicFormController::class, 'regenerateQR'])->name('regenerate.qr');
});

// API Routes - Statistics & QR Verification
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/stats', [PublicFormController::class, 'getStats'])->name('stats');
    Route::get('/verify/{token}', [PublicFormController::class, 'verifyQR'])->name('qr.verify');
});

// Alternative route for QR verification (shorter URL)
Route::get('/verify/{token}', [PublicFormController::class, 'verifyQR'])->name('qr.verify.short');