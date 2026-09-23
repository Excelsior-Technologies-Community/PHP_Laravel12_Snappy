<?php

namespace Tests\Feature;

use App\Models\PdfReportHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnappyAdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_watermarked_and_password_protected_pdf(): void
    {
        User::factory()->count(3)->create();

        $response = $this->get(route('pdf.generate', [
            'watermark' => 'CONFIDENTIAL',
            'pdf_password' => 'secret123',
            'report_title' => 'Protected Financial Invoice',
        ]));

        $response->assertStatus(200);

        $this->assertDatabaseHas('pdf_report_histories', [
            'report_type' => 'All Users Report',
        ]);
    }

    public function test_can_generate_high_res_image_via_snappy_image(): void
    {
        User::factory()->count(2)->create();

        $response = $this->get(route('pdf.image', [
            'format' => 'png',
            'quality' => 90,
            'width' => 800,
            'report_title' => 'User Card Image',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');

        $this->assertDatabaseHas('pdf_report_histories', [
            'report_type' => 'High-Res Image (PNG)',
        ]);
    }

    public function test_can_batch_export_individual_user_pdfs_into_zip_archive(): void
    {
        $users = User::factory()->count(3)->create();

        $response = $this->post(route('pdf.batch-zip'), [
            'user_ids' => $users->pluck('id')->toArray(),
            'watermark' => 'OFFICIAL',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/zip');

        $this->assertDatabaseHas('pdf_report_histories', [
            'report_type' => 'Batch PDF ZIP Archive',
        ]);
    }
}
