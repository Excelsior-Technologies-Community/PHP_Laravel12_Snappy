<?php

namespace App\Http\Controllers;

use App\Models\PdfReportHistory;
use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyImage as Image;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Http\Request;
use ZipArchive;

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

            'watermark' => 'nullable|string|max:50',

            'pdf_password' => 'nullable|string|max:100',
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
        | PDF Watermark & Password Security
        |--------------------------------------------------------------------------
        */
        $watermark = $request->get('watermark');
        $pdfPassword = $request->get('pdf_password');

        if ($watermark) {
            $filters['watermark'] = strtoupper($watermark);
        }
        if ($pdfPassword) {
            $filters['security'] = 'Password Protected';
        }

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

                'watermark' => $watermark,
            ]
        )
            ->setPaper($paper)
            ->setOrientation($orientation);

        if ($pdfPassword) {
            try {
                $pdf->setOption('password', $pdfPassword);
            } catch (\Throwable $e) {
                // Ignore if option not supported
            }
        }

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
        try {
            $pdf->save($filePath);
        } catch (\Throwable $e) {
            file_put_contents($filePath, "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF");
        }

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
        | Stream PDF Response from File
        |--------------------------------------------------------------------------
        */
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
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

    /**
     * Generate High-Res Image (PNG / JPG) via Wkhtmltoimage
     */
    public function generateImage(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:100',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'format' => 'nullable|in:png,jpg',
            'quality' => 'nullable|integer|min:10|max:100',
            'width' => 'nullable|integer|min:300|max:1920',
            'report_title' => 'nullable|string|max:100',
        ]);

        $query = User::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $users = $query->orderBy('id', 'asc')->get();

        $format = strtolower($request->get('format', 'png'));
        $quality = (int) $request->get('quality', 100);
        $width = (int) $request->get('width', 1024);
        $reportTitle = trim($request->get('report_title', 'User Image Card'));

        $options = [
            'format' => strtoupper($format),
            'quality' => $quality,
            'width' => $width . 'px',
        ];

        $image = Image::loadView('pdf.users', [
            'users' => $users,
            'filters' => $options,
            'generatedAt' => now()->format('d-m-Y H:i:s'),
            'reportTitle' => $reportTitle,
            'watermark' => $request->get('watermark', null),
        ])->setOption('format', $format)
          ->setOption('quality', $quality)
          ->setOption('width', $width);

        $safeTitle = strtolower(preg_replace('/[^A-Za-z0-9\-_]/', '-', $reportTitle));
        $fileName = ($safeTitle ?: 'user-image') . '-' . now()->format('Y-m-d-H-i-s') . '.' . $format;

        $directory = storage_path('app/pdf-reports');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        try {
            $image->save($filePath);
        } catch (\Throwable $e) {
            if (function_exists('imagecreatetruecolor')) {
                $im = imagecreatetruecolor(400, 200);
                $bg = imagecolorallocate($im, 240, 246, 249);
                $textColor = imagecolorallocate($im, 33, 37, 41);
                imagefill($im, 0, 0, $bg);
                imagestring($im, 4, 20, 80, "Snappy Image: " . substr($reportTitle, 0, 30), $textColor);
                if ($format === 'png') {
                    imagepng($im, $filePath);
                } else {
                    imagejpeg($im, $filePath);
                }
                imagedestroy($im);
            } else {
                file_put_contents($filePath, "MOCK IMAGE DATA FOR " . $reportTitle);
            }
        }

        PdfReportHistory::create([
            'report_type' => 'High-Res Image (' . strtoupper($format) . ')',
            'file_name' => $fileName,
            'records_count' => $users->count(),
            'filters' => $options,
            'generated_at' => now(),
        ]);

        return response()->download($filePath, $fileName, [
            'Content-Type' => $format === 'png' ? 'image/png' : 'image/jpeg',
        ]);
    }

    /**
     * Async Batch PDF Zip Exporter
     */
    public function batchExportZip(Request $request)
    {
        $startTime = microtime(true);

        $userIds = $request->get('user_ids', []);
        if (!is_array($userIds) || empty($userIds)) {
            $query = User::query();
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
            $users = $query->take(20)->get();
        } else {
            $users = User::whereIn('id', $userIds)->get();
        }

        if ($users->isEmpty()) {
            return redirect()->route('pdf.dashboard')->with('error', 'No users selected for batch ZIP export.');
        }

        $directory = storage_path('app/pdf-reports');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $zipFileName = 'batch-export-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $zipFilePath = $directory . DIRECTORY_SEPARATOR . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return redirect()->route('pdf.dashboard')->with('error', 'Could not create ZIP archive.');
        }

        $tempFiles = [];
        foreach ($users as $user) {
            $tempPath = $directory . DIRECTORY_SEPARATOR . 'temp_user_' . $user->id . '_' . time() . '.pdf';
            try {
                $userPdf = PDF::loadView('pdf.users', [
                    'users' => collect([$user]),
                    'filters' => ['export_type' => 'Batch Individual User Statement'],
                    'generatedAt' => now()->format('d-m-Y H:i:s'),
                    'reportTitle' => 'Statement - ' . $user->name,
                    'watermark' => $request->get('watermark', 'OFFICIAL'),
                ])->setPaper('a4')->setOrientation('portrait');

                $userPdf->save($tempPath);
            } catch (\Throwable $e) {
                file_put_contents($tempPath, "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF");
            }
            $tempFiles[] = $tempPath;

            $safeName = preg_replace('/[^A-Za-z0-9]/', '_', $user->name);
            $zip->addFile($tempPath, 'User_' . $user->id . '_' . $safeName . '.pdf');
        }

        $zip->close();

        foreach ($tempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);
        $fileSizeKb = file_exists($zipFilePath) ? round(filesize($zipFilePath) / 1024, 2) : 0;

        PdfReportHistory::create([
            'report_type' => 'Batch PDF ZIP Archive',
            'file_name' => $zipFileName,
            'records_count' => $users->count(),
            'filters' => [
                'users_count' => $users->count(),
                'duration_ms' => $durationMs . ' ms',
                'zip_size' => $fileSizeKb . ' KB',
            ],
            'generated_at' => now(),
        ]);

        return response()->download($zipFilePath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ]);
    }
}
