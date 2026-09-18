<?php

namespace App\Http\Controllers;

use App\Models\PdfReportHistory;
use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    /**
     * PDF Dashboard
     */
    public function dashboard(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter by start date
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // Filter by end date
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalUsers = User::count();
        $totalReports = PdfReportHistory::count();
        $totalRecordsExported = PdfReportHistory::sum('records_count');

        $recentReports = PdfReportHistory::latest('generated_at')
            ->take(10)
            ->get();

        return view('pdf.dashboard', compact(
            'users',
            'totalUsers',
            'totalReports',
            'totalRecordsExported',
            'recentReports'
        ));
    }

    /**
     * Generate the main dynamic user PDF report.
     */
    public function generate(Request $request)
    {
        $query = User::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Start date filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // End date filter
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $users = $query
            ->latest()
            ->get();

        $filters = [
            'search' => $request->search,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ];

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $pdf = PDF::loadView('pdf.users', [
            'users' => $users,
            'filters' => $filters,
            'generatedAt' => now()->format('d-m-Y H:i:s'),
        ])
            ->setPaper('a4')
            ->setOrientation('portrait');

        $fileName = 'user-report-' . now()->format('Y-m-d-H-i-s') . '.pdf';

        $directory = storage_path('app/pdf-reports');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        $pdf->save($filePath);

        PdfReportHistory::create([
            'report_type' => empty($filters)
                ? 'All Users Report'
                : 'Filtered Users Report',
            'file_name' => $fileName,
            'records_count' => $users->count(),
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        return $pdf->stream($fileName);
    }

    /**
     * Generate a PDF from the current dashboard filters.
     */
    public function filteredPdf(Request $request)
    {
        return $this->generate($request);
    }

    /**
     * Download an existing generated PDF.
     */
    public function download(PdfReportHistory $report)
    {
        $filePath = storage_path(
            'app/pdf-reports/' . $report->file_name
        );

        if (!file_exists($filePath)) {
            return redirect()
                ->route('pdf.dashboard')
                ->with('error', 'The selected PDF file no longer exists.');
        }

        return response()->download(
            $filePath,
            $report->file_name,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }
}