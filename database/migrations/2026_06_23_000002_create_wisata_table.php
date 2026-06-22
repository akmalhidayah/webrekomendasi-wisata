<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wisata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_wisata_id')->index()->constrained('kategori_wisata')->restrictOnDelete();
            $table->string('nama_wisata');
            $table->string('slug')->unique();
            $table->string('jenis_wisata');
            $table->longText('deskripsi')->nullable();
            $table->text('alamat');
            $table->string('kecamatan')->nullable();
            $table->string('kota')->default('Makassar');
            $table->string('provinsi')->default('Sulawesi Selatan');
            $table->text('link_maps')->nullable();
            $table->decimal('harga_tiket', 12, 2)->nullable()->default(0);
            $table->decimal('estimasi_transportasi', 12, 2)->nullable()->default(0);
            $table->decimal('estimasi_biaya_lainnya', 12, 2)->nullable()->default(0);
            $table->decimal('total_estimasi_biaya', 12, 2)->nullable()->default(0);
            $table->string('jam_operasional')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->index();
            $table->string('foto_utama')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wisata');
    }
};
