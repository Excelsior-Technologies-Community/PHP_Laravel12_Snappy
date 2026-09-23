<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        {{ $reportTitle ?? 'User Report' }}
    </title>

    <style>

        @page {
            margin: 25px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo {
            margin-bottom: 10px;
        }

        .logo img {
            width: 110px;
        }

        h1 {
            margin: 5px 0;
            font-size: 22px;
        }

        h2 {
            font-size: 16px;
            margin-top: 20px;
        }

        .report-info {
            margin-bottom: 20px;
        }

        .report-info table {
            width: 100%;
        }

        .report-info td {
            padding: 5px;
        }

        .filter-box {
            background: #f2f2f2;
            padding: 10px;
            margin-bottom: 20px;
        }

        .filter-box p {
            margin: 3px 0;
        }

        table.users {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.users th,
        table.users td {
            border: 1px solid #333;
            padding: 8px;
        }

        table.users th {
            background-color: #e9e9e9;
            font-weight: bold;
        }

        table.users tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        .summary {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
        }

        .watermark {
            position: fixed;
            top: 40%;
            left: 10%;
            width: 80%;
            text-align: center;
            opacity: 0.12;
            font-size: 65px;
            font-weight: bold;
            color: #dc3545;
            transform: rotate(-35deg);
            z-index: -1000;
        }

    </style>

</head>

<body>

@if(!empty($watermark))
    <div class="watermark">
        {{ strtoupper($watermark) }}
    </div>
@endif

<div class="header">

    <div class="logo">

        @if(file_exists(public_path('images/logo.png')))

            <img
                src="{{ public_path('images/logo.png') }}"
                alt="Company Logo">

        @endif

    </div>

    <h1>
        {{ $reportTitle ?? 'User Report' }}
    </h1>

    <p>
        Laravel 12 Snappy PDF Report
    </p>

</div>


<div class="report-info">

    <table>

        <tr>

            <td>
                <strong>Generated At:</strong>
            </td>

            <td>
                {{ $generatedAt }}
            </td>

            <td>
                <strong>Total Records:</strong>
            </td>

            <td>
                {{ $users->count() }}
            </td>

        </tr>

    </table>

</div>


@if(!empty($filters))

    <div class="filter-box">

        <strong>Applied Filters</strong>

        @foreach($filters as $key => $value)

            <p>

                <strong>
                    {{ ucfirst(str_replace('_', ' ', $key)) }}:
                </strong>

                {{ $value }}

            </p>

        @endforeach

    </div>

@endif


<h2>User Details</h2>


<table class="users">

    <thead>

        <tr>

            <th width="8%">ID</th>

            <th width="27%">Name</th>

            <th width="35%">Email</th>

            <th width="30%">Registered At</th>

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
                    {{ $user->created_at?->format('d-m-Y H:i:s') }}
                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="4"
                    style="text-align:center;">

                    No users found.

                </td>

            </tr>

        @endforelse

    </tbody>

</table>


<div class="summary">

    <strong>
        Total Users in This Report:
        {{ $users->count() }}
    </strong>

</div>


<div class="footer">

    <p>
        This is a system generated PDF report.
    </p>

    <p>
        Generated using Laravel Snappy and wkhtmltopdf.
    </p>

    <p>
        © {{ date('Y') }} Your Company Name.
        All Rights Reserved.
    </p>

</div>

</body>

</html>