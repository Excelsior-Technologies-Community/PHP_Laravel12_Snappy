<?php

namespace App\Http\Controllers;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class PdfController extends Controller
{
    public function generate()
    {
        // Generate and stream a PDF file using a Blade view

        $data = [
            'name' => 'Harry',
            'date' => now()->format('d-m-Y'),
        ];

        $pdf = PDF::loadView('pdf.test', $data)
                    ->setPaper('a4')
                    ->setOrientation('portrait');

        return $pdf->stream('sample.pdf');
        // return $pdf->download('sample.pdf'); // use this for download
    }
}
