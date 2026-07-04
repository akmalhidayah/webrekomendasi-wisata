<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hotel');
            $table->string('slug')->unique();
            $table->text('alamat')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_min', 12, 2)->default(0);
            $table->decimal('harga_max', 12, 2)->default(0);
            $table->string('gambar')->nullable();
            $table->text('traveloka_url')->nullable();
            $table->text('maps_url')->nullable();
            $table->decimal('rating_hotel', 2, 1)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
