<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_wisata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisata_id')->index()->constrained('wisata')->cascadeOnDelete();
            $table->string('path_foto');
            $table->string('caption')->nullable();
            $table->boolean('is_utama')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_wisata');
    }
};
