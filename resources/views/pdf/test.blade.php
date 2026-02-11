<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PDF Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .logo {
            margin-bottom: 10px;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-section p {
            margin: 5px 0;
        }

        .table-section {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th {
            background-color: #f2f2f2;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .summary {
            margin-top: 30px;
            text-align: right;
            font-size: 16px;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Header -->
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/logo.png') }}" width="120">
        </div>
        <h2>Company Report</h2>
        <p>Date: {{ $date }}</p>
    </div>

    <!-- User Info -->
    <div class="info-section">
        <h3>User Details</h3>
        <p><strong>Name:</strong> {{ $name }}</p>
        <p><strong>Email:</strong> harry@example.com</p>
        <p><strong>Phone:</strong> +91 9876543210</p>
        <p><strong>Address:</strong> Ahmedabad, Gujarat, India</p>
    </div>

    <!-- Table Section -->
    <div class="table-section">
        <h3>Activity Summary</h3>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Account Created</td>
                    <td>01-01-2024</td>
                    <td>Completed</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Profile Updated</td>
                    <td>15-02-2024</td>
                    <td>Completed</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Password Changed</td>
                    <td>20-03-2024</td>
                    <td>Completed</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Subscription Activated</td>
                    <td>05-04-2024</td>
                    <td>Active</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="summary">
        <p><strong>Total Activities:</strong> 4</p>
        <p><strong>Status:</strong> Active User</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a system generated PDF report.</p>
        <p>© 2026 Your Company Name. All Rights Reserved.</p>
    </div>

</div>

</body>
</html>
