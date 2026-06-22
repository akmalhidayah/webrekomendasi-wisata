<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_visitor_id')->index()->constrained('guest_visitors')->cascadeOnDelete();
            $table->foreignId('wisata_id')->index()->constrained('wisata')->cascadeOnDelete();
            $table->decimal('nilai_prediksi', 8, 4)->nullable();
            $table->decimal('nilai_similarity', 8, 4)->nullable();
            $table->unsignedInteger('ranking')->nullable();
            $table->string('metode')->default('Collaborative Filtering');
            $table->timestamps();

            $table->index(['guest_visitor_id', 'ranking']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_rekomendasi');
    }
};
