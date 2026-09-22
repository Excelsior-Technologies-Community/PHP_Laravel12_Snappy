<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| PDF Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/pdf-reports', [
    PdfController::class,
    'dashboard'
])->name('pdf.dashboard');

/*
|--------------------------------------------------------------------------
| Generate PDF
|--------------------------------------------------------------------------
*/

Route::get('/generate-pdf', [
    PdfController::class,
    'generate'
])->name('pdf.generate');

/*
|--------------------------------------------------------------------------
| Generate Filtered PDF
|--------------------------------------------------------------------------
*/

Route::get('/generate-filtered-pdf', [
    PdfController::class,
    'filteredPdf'
])->name('pdf.filtered');

/*
|--------------------------------------------------------------------------
| Preview Existing PDF
|--------------------------------------------------------------------------
*/

Route::get('/pdf-reports/{report}/preview', [
    PdfController::class,
    'preview'
])->name('pdf.preview');

/*
|--------------------------------------------------------------------------
| Download Existing PDF
|--------------------------------------------------------------------------
*/

Route::get('/pdf-reports/{report}/download', [
    PdfController::class,
    'download'
])->name('pdf.download');

/*
|--------------------------------------------------------------------------
| Delete One PDF Report
|--------------------------------------------------------------------------
*/

Route::delete('/pdf-reports/{report}', [
    PdfController::class,
    'destroy'
])->name('pdf.destroy');

/*
|--------------------------------------------------------------------------
| Bulk Delete PDF Reports
|--------------------------------------------------------------------------
*/

Route::delete('/pdf-reports-bulk-delete', [
    PdfController::class,
    'bulkDelete'
])->name('pdf.bulk-delete');