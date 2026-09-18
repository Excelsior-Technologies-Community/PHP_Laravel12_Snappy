<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>PDF Report Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

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
            max-width: 1250px;
            margin: auto;
        }

        .header {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: #ffffff;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 30px;
            font-weight: bold;
        }

        .filter-box {
            background: #ffffff;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .filter-box h2 {
            margin-top: 0;
            font-size: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto auto;
            gap: 12px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
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

        .table-box {
            background: #ffffff;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        .table-box h2 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
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
            font-size: 12px;
            background: #e9ecef;
        }

        .history-table {
            font-size: 14px;
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
        }

        @media (max-width: 900px) {
            .cards {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <h1>📊 PDF Report Dashboard</h1>
        <p>Laravel 12 Snappy PDF Generation & Report Management</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

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
                Generated PDF Reports
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

    </div>

    <div class="filter-box">

        <h2>🔎 Search & Filter Users</h2>

        <form method="GET" action="{{ route('pdf.dashboard') }}">

            <div class="form-grid">

                <div class="form-group">
                    <label>Search Name / Email</label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Enter name or email">
                </div>

                <div class="form-group">
                    <label>From Date</label>

                    <input
                        type="date"
                        name="from_date"
                        value="{{ request('from_date') }}">
                </div>

                <div class="form-group">
                    <label>To Date</label>

                    <input
                        type="date"
                        name="to_date"
                        value="{{ request('to_date') }}">
                </div>

                <div>
                    <button
                        type="submit"
                        class="btn btn-primary">
                        Search
                    </button>
                </div>

                <div>
                    <a
                        href="{{ route('pdf.dashboard') }}"
                        class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </div>

        </form>

        <br>

        <a
            href="{{ route('pdf.generate', request()->query()) }}"
            target="_blank"
            class="btn btn-success">
            📄 Generate Filtered PDF
        </a>

        <a
            href="{{ route('pdf.generate') }}"
            target="_blank"
            class="btn btn-dark">
            📊 Generate All Users PDF
        </a>

    </div>

    <div class="table-box">

        <h2>👥 User Records</h2>

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
                        <td colspan="4">
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

    <div class="table-box">

        <h2>📑 PDF Report History</h2>

        <table class="history-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Report Type</th>
                    <th>Records</th>
                    <th>Filters</th>
                    <th>Generated</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

                @forelse($recentReports as $report)

                    <tr>

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
                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
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

                            <a
                                href="{{ route('pdf.download', $report) }}"
                                class="btn btn-primary">
                                ⬇ Download
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            No PDF reports have been generated yet.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

</body>

</html>