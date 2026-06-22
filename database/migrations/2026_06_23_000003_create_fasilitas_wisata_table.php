<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas_wisata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisata_id')->index()->constrained('wisata')->cascadeOnDelete();
            $table->string('nama_fasilitas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas_wisata');
    }
};
