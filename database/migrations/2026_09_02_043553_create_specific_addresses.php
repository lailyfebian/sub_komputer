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
        Schema::create('specific_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')
                ->constrained('regions')
                ->cascadeOnDelete();
            $table->enum('category', ['sd_negeri', 'smp_negeri', 'sma_negeri', 'sd_swasta', 'smp_swasta', 'sma_swasta', 'mts', 'ma']);
            $table->timestamps();
            $table->unique(['region_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specific_addresses');
    }
};
