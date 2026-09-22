<?php

namespace App\Http\Controllers;

use App\Models\PdfReportHistory;
use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * PDF Dashboard
     */
    public function dashboard(Request $request)
    {
        $query = User::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | From Date
        |--------------------------------------------------------------------------
        */
        if ($request->filled('from_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | To Date
        |--------------------------------------------------------------------------
        */
        if ($request->filled('to_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | User Sorting
        |--------------------------------------------------------------------------
        |
        | Default:
        | ID - Ascending
        |
        */
        $sortBy = $request->get('sort_by', 'id');

        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSorts = [
            'id',
            'name',
            'email',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'asc';
        }

        /*
        |--------------------------------------------------------------------------
        | User Records
        |--------------------------------------------------------------------------
        |
        | Default result:
        | 1, 2, 3, 4, 5, 6, 7
        |
        */
        $users = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Main Statistics
        |--------------------------------------------------------------------------
        */
        $totalUsers = User::count();

        $totalReports = PdfReportHistory::count();

        $totalRecordsExported = PdfReportHistory::sum(
            'records_count'
        );

        /*
        |--------------------------------------------------------------------------
        | Today's Statistics
        |--------------------------------------------------------------------------
        */
        $todayReports = PdfReportHistory::whereDate(
            'generated_at',
            today()
        )->count();

        $todayRecords = PdfReportHistory::whereDate(
            'generated_at',
            today()
        )->sum('records_count');

        /*
        |--------------------------------------------------------------------------
        | Current Month Statistics
        |--------------------------------------------------------------------------
        */
        $monthReports = PdfReportHistory::whereMonth(
            'generated_at',
            now()->month
        )
            ->whereYear(
                'generated_at',
                now()->year
            )
            ->count();

        $monthRecords = PdfReportHistory::whereMonth(
            'generated_at',
            now()->month
        )
            ->whereYear(
                'generated_at',
                now()->year
            )
            ->sum('records_count');

        /*
        |--------------------------------------------------------------------------
        | PDF Report History
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | History is ALWAYS displayed by ID ASC.
        |
        | Example:
        | 1
        | 2
        | 3
        | 4
        | 5
        | 6
        | 7
        |
        */
        $recentReports = PdfReportHistory::query()
            ->orderBy('id', 'asc')
            ->paginate(
                5,
                ['*'],
                'reports_page'
            )
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */
        return view(
            'pdf.dashboard',
            compact(
                'users',
                'totalUsers',
                'totalReports',
                'totalRecordsExported',
                'todayReports',
                'todayRecords',
                'monthReports',
                'monthRecords',
                'recentReports',
                'sortBy',
                'sortOrder'
            )
        );
    }

    /**
     * Generate PDF.
     */
    public function generate(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'search' => 'nullable|string|max:100',

            'from_date' => 'nullable|date',

            'to_date' => 'nullable|date|after_or_equal:from_date',

            'sort_by' => 'nullable|in:id,name,email,created_at',

            'sort_order' => 'nullable|in:asc,desc',

            'orientation' => 'nullable|in:portrait,landscape',

            'paper' => 'nullable|in:a4,letter,legal',

            'report_title' => 'nullable|string|max:100',
        ]);

        /*
        |--------------------------------------------------------------------------
        | User Query
        |--------------------------------------------------------------------------
        */
        $query = User::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere(
                        'email',
                        'like',
                        '%' . $search . '%'
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | From Date
        |--------------------------------------------------------------------------
        */
        if ($request->filled('from_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | To Date
        |--------------------------------------------------------------------------
        */
        if ($request->filled('to_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        $sortBy = $request->get(
            'sort_by',
            'id'
        );

        $sortOrder = $request->get(
            'sort_order',
            'asc'
        );

        $allowedSorts = [
            'id',
            'name',
            'email',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'asc';
        }

        /*
        |--------------------------------------------------------------------------
        | Get Users For PDF
        |--------------------------------------------------------------------------
        */
        $users = $query
            ->orderBy($sortBy, $sortOrder)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PDF Orientation
        |--------------------------------------------------------------------------
        */
        $orientation = $request->get(
            'orientation',
            'portrait'
        );

        /*
        |--------------------------------------------------------------------------
        | PDF Paper
        |--------------------------------------------------------------------------
        */
        $paper = $request->get(
            'paper',
            'a4'
        );

        /*
        |--------------------------------------------------------------------------
        | Report Title
        |--------------------------------------------------------------------------
        */
        $reportTitle = trim(
            $request->get(
                'report_title',
                'User Report'
            )
        );

        if ($reportTitle === '') {
            $reportTitle = 'User Report';
        }

        /*
        |--------------------------------------------------------------------------
        | Filters Displayed In PDF
        |--------------------------------------------------------------------------
        */
        $filters = [
            'search' => $request->search,

            'from_date' => $request->from_date,

            'to_date' => $request->to_date,

            'sort_by' => $sortBy,

            'sort_order' => $sortOrder,

            'orientation' => ucfirst(
                $orientation
            ),

            'paper' => strtoupper(
                $paper
            ),
        ];

        $filters = array_filter(
            $filters,
            function ($value) {
                return $value !== null
                    && $value !== '';
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */
        $pdf = PDF::loadView(
            'pdf.users',
            [
                'users' => $users,

                'filters' => $filters,

                'generatedAt' => now()->format(
                    'd-m-Y H:i:s'
                ),

                'reportTitle' => $reportTitle,
            ]
        )
            ->setPaper($paper)
            ->setOrientation($orientation);

        /*
        |--------------------------------------------------------------------------
        | Safe File Name
        |--------------------------------------------------------------------------
        */
        $safeTitle = preg_replace(
            '/[^A-Za-z0-9\-_]/',
            '-',
            $reportTitle
        );

        $safeTitle = trim(
            $safeTitle,
            '-'
        );

        if ($safeTitle === '') {
            $safeTitle = 'user-report';
        }

        $fileName =
            strtolower($safeTitle)
            . '-'
            . now()->format(
                'Y-m-d-H-i-s'
            )
            . '.pdf';

        /*
        |--------------------------------------------------------------------------
        | Storage Directory
        |--------------------------------------------------------------------------
        */
        $directory = storage_path(
            'app/pdf-reports'
        );

        if (!is_dir($directory)) {
            mkdir(
                $directory,
                0755,
                true
            );
        }

        $filePath =
            $directory
            . DIRECTORY_SEPARATOR
            . $fileName;

        /*
        |--------------------------------------------------------------------------
        | Save PDF
        |--------------------------------------------------------------------------
        */
        $pdf->save($filePath);

        /*
        |--------------------------------------------------------------------------
        | Determine Report Type
        |--------------------------------------------------------------------------
        */
        $reportType = empty(
            array_intersect_key(
                $filters,
                array_flip([
                    'search',
                    'from_date',
                    'to_date',
                ])
            )
        )
            ? 'All Users Report'
            : 'Filtered Users Report';

        /*
        |--------------------------------------------------------------------------
        | Save History
        |--------------------------------------------------------------------------
        */
        PdfReportHistory::create([
            'report_type' => $reportType,

            'file_name' => $fileName,

            'records_count' => $users->count(),

            'filters' => $filters,

            'generated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Preview PDF
        |--------------------------------------------------------------------------
        */
        return $pdf->stream(
            $fileName
        );
    }

    /**
     * Generate filtered PDF.
     */
    public function filteredPdf(Request $request)
    {
        return $this->generate($request);
    }

    /**
     * Download existing PDF.
     */
    public function download(
        PdfReportHistory $report
    ) {
        $filePath = storage_path(
            'app/pdf-reports/'
            . $report->file_name
        );

        if (!file_exists($filePath)) {
            return redirect()
                ->route('pdf.dashboard')
                ->with(
                    'error',
                    'The selected PDF file no longer exists.'
                );
        }

        return response()->download(
            $filePath,
            $report->file_name,
            [
                'Content-Type' =>
                    'application/pdf',
            ]
        );
    }

    /**
     * Preview an existing PDF.
     */
    public function preview(
        PdfReportHistory $report
    ) {
        $filePath = storage_path(
            'app/pdf-reports/'
            . $report->file_name
        );

        if (!file_exists($filePath)) {
            return redirect()
                ->route('pdf.dashboard')
                ->with(
                    'error',
                    'The selected PDF file no longer exists.'
                );
        }

        return response()->file(
            $filePath,
            [
                'Content-Type' =>
                    'application/pdf',
            ]
        );
    }

    /**
     * Delete one PDF report.
     */
    public function destroy(
        PdfReportHistory $report
    ) {
        $filePath = storage_path(
            'app/pdf-reports/'
            . $report->file_name
        );

        /*
        |--------------------------------------------------------------------------
        | Delete Physical PDF
        |--------------------------------------------------------------------------
        */
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        /*
        |--------------------------------------------------------------------------
        | Delete History Record
        |--------------------------------------------------------------------------
        */
        $report->delete();

        return redirect()
            ->route('pdf.dashboard')
            ->with(
                'success',
                'PDF report deleted successfully.'
            );
    }

    /**
     * Bulk delete PDF reports.
     */
    public function bulkDelete(
        Request $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | Validate Selected IDs
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'report_ids' => 'required|array',

            'report_ids.*' =>
                'integer|exists:pdf_report_histories,id',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get Selected Reports
        |--------------------------------------------------------------------------
        */
        $reports = PdfReportHistory::whereIn(
            'id',
            $request->report_ids
        )->get();

        $deletedCount = 0;

        /*
        |--------------------------------------------------------------------------
        | Delete Files + Database Records
        |--------------------------------------------------------------------------
        */
        foreach ($reports as $report) {

            $filePath = storage_path(
                'app/pdf-reports/'
                . $report->file_name
            );

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $report->delete();

            $deletedCount++;
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect With Success Message
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('pdf.dashboard')
            ->with(
                'success',
                $deletedCount
                . ' PDF report(s) deleted successfully.'
            );
    }
}
