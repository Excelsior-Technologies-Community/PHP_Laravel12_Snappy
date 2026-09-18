<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdf_report_histories', function (Blueprint $table) {
            $table->id();

            $table->string('report_type');
            $table->string('file_name');
            $table->unsignedInteger('records_count')->default(0);

            $table->json('filters')->nullable();

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_report_histories');
    }
};