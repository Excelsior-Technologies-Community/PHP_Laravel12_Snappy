<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| PDF Report Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/pdf-reports', [
    PdfController::class,
    'dashboard'
])->name('pdf.dashboard');

/*
|--------------------------------------------------------------------------
| Generate Dynamic User PDF
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
| Download Previous PDF Report
|--------------------------------------------------------------------------
*/

Route::get('/pdf-reports/{report}/download', [
    PdfController::class,
    'download'
])->name('pdf.download');