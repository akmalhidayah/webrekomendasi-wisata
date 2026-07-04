<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wisata_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisata_id')->constrained('wisata')->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['wisata_id', 'hotel_id']);
            $table->unique(['wisata_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wisata_hotels');
    }
};
