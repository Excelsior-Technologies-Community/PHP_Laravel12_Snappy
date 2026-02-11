#  PHP_Laravel12_Snappy

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Snappy](https://img.shields.io/badge/Laravel--Snappy-PDF-green)
![wkhtmltopdf](https://img.shields.io/badge/wkhtmltopdf-required-orange)

---

##  Overview

**PHP_Laravel12_Snappy** is a Laravel 12 project demonstrating how to generate PDF files using **barryvdh/laravel-snappy** with **wkhtmltopdf**.

This setup allows you to:

* Generate PDFs from Blade views
* Stream PDF in browser
* Download PDF files
* Add images (like logo) inside PDF
* Use custom CSS styling

---

##  Features

* Laravel 12 fresh installation
* Snappy PDF integration
* wkhtmltopdf binary configuration (Windows)
* Blade-based PDF template
* Company-style report layout
* Logo support inside PDF
* Stream or Download PDF option

---

##  Folder Structure

```
snappy-project/
│
├── app/
│   └── Http/
│       └── Controllers/
│           └── PdfController.php
│
├── config/
│   └── snappy.php
│
├── resources/
│   └── views/
│       └── pdf/
│           └── test.blade.php
│
├── public/
│   └── images/
│       └── logo.png
│
├── routes/
│   └── web.php
│
└── .env
```

---

#  Installation Guide

---

## 1️ Create New Laravel 12 Project

```bash
composer create-project laravel/laravel snappy-project
```

Start server:

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

## 2️ Database Configuration

Update `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

## 3️ Install Laravel Snappy

```bash
composer require barryvdh/laravel-snappy
```

Laravel 12 supports auto-discovery. No manual provider registration required.

---

## 4️ Install wkhtmltopdf (Required)

Download from:

[https://wkhtmltopdf.org/downloads.html](https://wkhtmltopdf.org/downloads.html)

<img width="689" height="748" alt="Screenshot 2026-02-11 115710" src="https://github.com/user-attachments/assets/df699373-9c23-447c-9307-441f5bfcc8dd" />


Install in default location:

```
C:\Program Files\wkhtmltopdf
```

Confirm binary exists:

```
C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe
```

Optional test:

```bash
"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe" -V
```

---

## 5️ Publish Configuration

```bash
php artisan vendor:publish --provider="Barryvdh\Snappy\ServiceProvider"
```

This creates:

```
config/snappy.php
```

---

## 6️ Configure Binary Path

Open `config/snappy.php` and update:

```php
'pdf' => [
    'enabled' => true,
    'binary'  => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"',
    'timeout' => false,
    'options' => [
        'enable-local-file-access' => true,
    ],
],
```

Clear cache:

```bash
php artisan config:clear

php artisan cache:clear
```

---

## 7️ Create Controller

```bash
php artisan make:controller PdfController
```

Replace controller with:

```php
<?php

namespace App\Http\Controllers;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class PdfController extends Controller
{
    public function generate()
    {
        $data = [
            'name' => 'Harry',
            'date' => now()->format('d-m-Y'),
        ];

        $pdf = PDF::loadView('pdf.test', $data)
                    ->setPaper('a4')
                    ->setOrientation('portrait');

        return $pdf->stream('sample.pdf');
        // return $pdf->download('sample.pdf');
    }
}
```

---

## 8️ Add Route

In `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/generate-pdf', [PdfController::class, 'generate']);
```

---

## 9️ Create Blade View

Create folder:

```
resources/views/pdf/
```

Create file:

```
test.blade.php
```
```
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

```

## 10 Add Logo

Place logo in:

```
public/images/logo.png
```

Inside PDF use:

```php
{{ public_path('images/logo.png') }}
```

Do NOT use :
```php
{{ asset('images/logo.png') }}

```

## 11 Test PDF

Run server:

```bash
php artisan serve
```

Open:

```
http://127.0.0.1:8000/generate-pdf
```
<img width="1904" height="943" alt="Screenshot 2026-02-11 115243" src="https://github.com/user-attachments/assets/7b3d444f-980e-4557-bed8-f3dea0b63955" />


If PDF opens → Setup Successful 🎉

---

#  Notes

* wkhtmltopdf must be installed correctly
* Binary path must match your system
* Always clear config cache after editing snappy config
* Use public_path() for images inside PDF

---


