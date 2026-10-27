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
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            // Relasi ke template keyword
            $table->foreignId('keyword_template_id')
                ->constrained('keyword_templates')
                ->cascadeOnDelete();

            // Relasi wilayah (opsional)
            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();

            // Tipe wilayah: kota, kecamatan, kelurahan, desa
            $table->string('region_type')->nullable();

            // Parent kota (untuk kecamatan / kelurahan / desa)
            $table->foreignId('parent_city_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();

            // Parent kecamatan (untuk kelurahan / desa)
            $table->foreignId('parent_district_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();

            // Informasi utama konten
            $table->string('title');
            $table->string('slug')->unique();

            // Section data (JSON dari repeater)
            $table->json('data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};