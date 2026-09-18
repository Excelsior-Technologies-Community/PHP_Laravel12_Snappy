<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfReportHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'file_name',
        'records_count',
        'filters',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'generated_at' => 'datetime',
        ];
    }
}