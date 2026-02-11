<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return view('welcome');
});

// Route to generate and display the PDF file
Route::get('/generate-pdf', [PdfController::class, 'generate']);
