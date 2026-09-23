<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>PDF Report Dashboard</title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            color: #212529;
        }

        .container {
            max-width: 1350px;
            margin: auto;
        }

        .header {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .header p {
            margin: 0;
            color: #6c757d;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 18px;
        }

        .card {
            background: #ffffff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .card-title {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 28px;
            font-weight: bold;
        }

        .filter-box,
        .table-box {
            background: #ffffff;
            padding: 22px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .filter-box h2,
        .table-box h2 {
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns:
                2fr
                1fr
                1fr
                1fr
                1fr;
            gap: 12px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            background: white;
        }

        .btn {
            display: inline-block;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            white-space: nowrap;
        }

        .btn-primary {
            background: #0d6efd;
            color: white;
        }

        .btn-success {
            background: #198754;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-dark {
            background: #212529;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #000;
        }

        .btn-small {
            padding: 7px 10px;
            font-size: 12px;
        }

        .action-row {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 11px;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f8f9fa;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            background: #e9ecef;
        }

        .alert {
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .alert-danger {
            background: #f8d7da;
            color: #842029;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 7px 11px;
            margin-right: 4px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            text-decoration: none;
            color: #212529;
        }

        .pagination .active span {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .report-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .empty {
            text-align: center;
            color: #6c757d;
            padding: 25px;
        }

        .bulk-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 7px;
        }

        .statistics-small {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .small-card {
            background: white;
            padding: 16px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        }

        .small-card strong {
            display: block;
            font-size: 22px;
            margin-top: 5px;
        }

        @media (max-width: 1100px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-grid {
                grid-template-columns: 1fr 1fr;
            }

            .statistics-small {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 700px) {

            body {
                padding: 15px;
            }

            .cards,
            .statistics-small,
            .form-grid {
                grid-template-columns: 1fr;
            }

            table {
                min-width: 800px;
            }

            .table-box {
                overflow-x: auto;
            }

        }

    </style>

</head>


<body>

<div class="container">


    <!-- HEADER -->

    <div class="header">

        <h1>
            📊 PDF Report Dashboard
        </h1>

        <p>
            Laravel 12 + Snappy PDF Generation & Report Management
        </p>

    </div>


    <!-- ALERTS -->

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    <!-- MAIN STATISTICS -->

    <div class="cards">

        <div class="card">

            <div class="card-title">
                Total Users
            </div>

            <div class="card-value">
                {{ $totalUsers }}
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Total PDF Reports
            </div>

            <div class="card-value">
                {{ $totalReports }}
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Total Records Exported
            </div>

            <div class="card-value">
                {{ $totalRecordsExported }}
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Reports This Month
            </div>

            <div class="card-value">
                {{ $monthReports }}
            </div>

        </div>

    </div>


    <!-- SMALL STATISTICS -->

    <div class="statistics-small">

        <div class="small-card">

            Today's Reports

            <strong>
                {{ $todayReports }}
            </strong>

        </div>


        <div class="small-card">

            Today's Records

            <strong>
                {{ $todayRecords }}
            </strong>

        </div>


        <div class="small-card">

            This Month Records

            <strong>
                {{ $monthRecords }}
            </strong>

        </div>


        <div class="small-card">

            Current Month

            <strong>
                {{ now()->format('F Y') }}
            </strong>

        </div>

    </div>


    <!-- FILTER -->

    <div class="filter-box">

        <h2>
            🔎 Search, Filter & PDF Settings
        </h2>


        <form
            method="GET"
            action="{{ route('pdf.dashboard') }}">

            <div class="form-grid">


                <!-- SEARCH -->

                <div class="form-group">

                    <label>
                        Search Name / Email
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Enter name or email">

                </div>


                <!-- FROM DATE -->

                <div class="form-group">

                    <label>
                        From Date
                    </label>

                    <input
                        type="date"
                        name="from_date"
                        value="{{ request('from_date') }}">

                </div>


                <!-- TO DATE -->

                <div class="form-group">

                    <label>
                        To Date
                    </label>

                    <input
                        type="date"
                        name="to_date"
                        value="{{ request('to_date') }}">

                </div>


                <!-- SORT -->

                <div class="form-group">

                    <label>
                        Sort By
                    </label>

                    <select name="sort_by">

                        <option
                            value="created_at"
                            @selected(request('sort_by', 'created_at') === 'created_at')>
                            Registered Date
                        </option>

                        <option
                            value="id"
                            @selected(request('sort_by') === 'id')>
                            ID
                        </option>

                        <option
                            value="name"
                            @selected(request('sort_by') === 'name')>
                            Name
                        </option>

                        <option
                            value="email"
                            @selected(request('sort_by') === 'email')>
                            Email
                        </option>

                    </select>

                </div>


                <!-- ORDER -->

                <div class="form-group">

                    <label>
                        Order
                    </label>

                    <select name="sort_order">

                        <option
                            value="desc"
                            @selected(request('sort_order', 'desc') === 'desc')>
                            Descending
                        </option>

                        <option
                            value="asc"
                            @selected(request('sort_order') === 'asc')>
                            Ascending
                        </option>

                    </select>

                </div>


                <!-- ORIENTATION -->

                <div class="form-group">

                    <label>
                        PDF Orientation
                    </label>

                    <select name="orientation">

                        <option
                            value="portrait"
                            @selected(request('orientation', 'portrait') === 'portrait')>
                            Portrait
                        </option>

                        <option
                            value="landscape"
                            @selected(request('orientation') === 'landscape')>
                            Landscape
                        </option>

                    </select>

                </div>


                <!-- PAPER -->

                <div class="form-group">

                    <label>
                        Paper Size
                    </label>

                    <select name="paper">

                        <option
                            value="a4"
                            @selected(request('paper', 'a4') === 'a4')>
                            A4
                        </option>

                        <option
                            value="letter"
                            @selected(request('paper') === 'letter')>
                            Letter
                        </option>

                        <option
                            value="legal"
                            @selected(request('paper') === 'legal')>
                            Legal
                        </option>

                    </select>

                </div>


                <!-- REPORT TITLE -->

                <div class="form-group">

                    <label>
                        Report Title
                    </label>

                    <input
                        type="text"
                        name="report_title"
                        value="{{ request('report_title', 'User Report') }}"
                        placeholder="Report title">

                </div>


                <!-- WATERMARK -->

                <div class="form-group">

                    <label>
                        Live Watermark
                    </label>

                    <select name="watermark">

                        <option value="" @selected(!request('watermark'))>
                            None
                        </option>

                        <option value="CONFIDENTIAL" @selected(request('watermark') === 'CONFIDENTIAL')>
                            🔴 CONFIDENTIAL
                        </option>

                        <option value="DRAFT" @selected(request('watermark') === 'DRAFT')>
                            🟠 DRAFT
                        </option>

                        <option value="PAID" @selected(request('watermark') === 'PAID')>
                            🟢 PAID
                        </option>

                        <option value="OFFICIAL" @selected(request('watermark') === 'OFFICIAL')>
                            🔵 OFFICIAL
                        </option>

                    </select>

                </div>


                <!-- ENCRYPTION PASSWORD -->

                <div class="form-group">

                    <label>
                        PDF Password Security
                    </label>

                    <input
                        type="password"
                        name="pdf_password"
                        value="{{ request('pdf_password') }}"
                        placeholder="Optional Lock Password">

                </div>

            </div>


            <div class="action-row">

                <button
                    type="submit"
                    class="btn btn-primary">

                    🔎 Apply Filters

                </button>


                <a
                    href="{{ route('pdf.dashboard') }}"
                    class="btn btn-secondary">

                    Reset

                </a>

            </div>

        </form>


        <!-- PDF & IMAGE & BATCH ZIP BUTTONS -->

        <div class="action-row">

            <a
                href="{{ route('pdf.generate', array_merge(request()->query(), [
                    'orientation' => request('orientation', 'portrait'),
                    'paper' => request('paper', 'a4'),
                    'report_title' => request('report_title', 'User Report'),
                    'watermark' => request('watermark'),
                    'pdf_password' => request('pdf_password'),
                ])) }}"
                target="_blank"
                class="btn btn-success">

                👁 Preview PDF

            </a>


            <a
                href="{{ route('pdf.generate', array_merge(request()->query(), [
                    'orientation' => request('orientation', 'portrait'),
                    'paper' => request('paper', 'a4'),
                    'report_title' => request('report_title', 'User Report'),
                    'watermark' => request('watermark'),
                    'pdf_password' => request('pdf_password'),
                ])) }}"
                target="_blank"
                class="btn btn-dark">

                📄 Generate PDF

            </a>


            <a
                href="{{ route('pdf.image', array_merge(request()->query(), [
                    'format' => 'png',
                    'quality' => 100,
                    'width' => 1024,
                    'report_title' => request('report_title', 'User Image Card'),
                ])) }}"
                target="_blank"
                class="btn btn-warning">

                📸 Generate High-Res Image (PNG)

            </a>


            <form action="{{ route('pdf.batch-zip') }}" method="POST" style="display:inline;">

                @csrf

                <input type="hidden" name="search" value="{{ request('search') }}">

                <button type="submit" class="btn" style="background: #6f42c1; color: white;">

                    ⚡ Batch Export Individual PDFs (.ZIP)

                </button>

            </form>

        </div>

    </div>


    <!-- USER TABLE -->

    <div class="table-box">

        <h2>
            👥 User Records
        </h2>


        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Registered</th>

                </tr>

            </thead>


            <tbody>

                @forelse($users as $user)

                    <tr>

                        <td>
                            {{ $user->id }}
                        </td>

                        <td>
                            {{ $user->name }}
                        </td>

                        <td>
                            {{ $user->email }}
                        </td>

                        <td>
                            {{ $user->created_at?->format('d-m-Y H:i') }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="4"
                            class="empty">

                            No users found for the selected filters.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>


        <div class="pagination">

            {{ $users->links() }}

        </div>

    </div>


    <!-- PDF HISTORY -->

    <div class="table-box">

        <h2>
            📑 PDF Report History
        </h2>


        <form
            method="POST"
            action="{{ route('pdf.bulk-delete') }}"
            id="bulkDeleteForm">

            @csrf

            @method('DELETE')


            <div class="bulk-bar">

                <div>

                    <label>

                        <input
                            type="checkbox"
                            id="selectAll">

                        Select All

                    </label>

                    <span id="selectedCount">
                        0 selected
                    </span>

                </div>


                <button
                    type="submit"
                    class="btn btn-danger"
                    id="bulkDeleteButton"
                    disabled>

                    🗑 Delete Selected

                </button>

            </div>


            <table>

                <thead>

                    <tr>

                        <th>
                            <input
                                type="checkbox"
                                id="headerSelectAll">
                        </th>

                        <th>ID</th>

                        <th>Report Type</th>

                        <th>Records</th>

                        <th>Filters</th>

                        <th>Generated</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($recentReports as $report)

                        <tr>

                            <td>

                                <input
                                    type="checkbox"
                                    name="report_ids[]"
                                    value="{{ $report->id }}"
                                    class="report-checkbox">

                            </td>


                            <td>
                                {{ $report->id }}
                            </td>


                            <td>

                                <span class="badge">
                                    {{ $report->report_type }}
                                </span>

                            </td>


                            <td>
                                {{ $report->records_count }}
                            </td>


                            <td>

                                @if(!empty($report->filters))

                                    @foreach($report->filters as $key => $value)

                                        <div>

                                            <strong>
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                            </strong>

                                            {{ $value }}

                                        </div>

                                    @endforeach

                                @else

                                    All Users

                                @endif

                            </td>


                            <td>

                                {{ $report->generated_at?->format('d-m-Y H:i:s') }}

                            </td>


                            <td>

                                <div class="report-actions">

                                    <!-- Preview -->

                                    <a
                                        href="{{ route('pdf.preview', $report) }}"
                                        target="_blank"
                                        class="btn btn-warning btn-small">

                                        👁 Preview

                                    </a>


                                    <!-- Download -->

                                    <a
                                        href="{{ route('pdf.download', $report) }}"
                                        class="btn btn-primary btn-small">

                                        ⬇ Download

                                    </a>


                                    <!-- Delete -->

                                    <button
                                        type="button"
                                        class="btn btn-danger btn-small"
                                        onclick="deleteReport({{ $report->id }})">

                                        🗑 Delete

                                    </button>

                                </div>


                                <!-- Individual delete form -->

                                <form
                                    id="delete-form-{{ $report->id }}"
                                    method="POST"
                                    action="{{ route('pdf.destroy', $report) }}"
                                    style="display:none;">

                                    @csrf

                                    @method('DELETE')

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="empty">

                                No PDF reports have been generated yet.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </form>


        <!-- HISTORY PAGINATION -->

        <div class="pagination">

            {{ $recentReports->links() }}

        </div>

    </div>


</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | Individual Delete
    |--------------------------------------------------------------------------
    */

    function deleteReport(id)
    {
        const confirmed = confirm(
            'Are you sure you want to delete this PDF report?'
        );

        if (!confirmed) {
            return;
        }

        document
            .getElementById('delete-form-' + id)
            .submit();
    }


    /*
    |--------------------------------------------------------------------------
    | Select All
    |--------------------------------------------------------------------------
    */

    const selectAll =
        document.getElementById('selectAll');

    const headerSelectAll =
        document.getElementById('headerSelectAll');

    const checkboxes =
        document.querySelectorAll('.report-checkbox');

    const bulkButton =
        document.getElementById('bulkDeleteButton');

    const selectedCount =
        document.getElementById('selectedCount');


    function updateBulkButton()
    {
        const selected =
            document.querySelectorAll(
                '.report-checkbox:checked'
            );

        const count =
            selected.length;

        selectedCount.textContent =
            count + ' selected';

        bulkButton.disabled =
            count === 0;

        selectAll.checked =
            count > 0 &&
            count === checkboxes.length;

        headerSelectAll.checked =
            selectAll.checked;
    }


    function toggleAll()
    {
        checkboxes.forEach(function (checkbox) {

            checkbox.checked =
                selectAll.checked;

        });

        updateBulkButton();
    }


    selectAll.addEventListener(
        'change',
        toggleAll
    );


    headerSelectAll.addEventListener(
        'change',
        function () {

            selectAll.checked =
                headerSelectAll.checked;

            toggleAll();

        }
    );


    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener(
            'change',
            updateBulkButton
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Bulk Delete Confirmation
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('bulkDeleteForm')
        .addEventListener(
            'submit',
            function (event) {

                const selected =
                    document.querySelectorAll(
                        '.report-checkbox:checked'
                    );

                if (selected.length === 0) {

                    event.preventDefault();

                    alert(
                        'Please select at least one PDF report.'
                    );

                    return;
                }

                const confirmed = confirm(
                    'Are you sure you want to delete '
                    + selected.length
                    + ' selected PDF report(s)?'
                );

                if (!confirmed) {
                    event.preventDefault();
                }

            }
        );

</script>

</body>

</html>