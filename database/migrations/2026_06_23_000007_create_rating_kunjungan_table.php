<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rating_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_visitor_id')->nullable()->index()->constrained('guest_visitors')->nullOnDelete();
            $table->foreignId('wisata_id')->index()->constrained('wisata')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('ulasan')->nullable();
            $table->boolean('pernah_dikunjungi')->default(true);
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending')->index();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE rating_kunjungan ADD CONSTRAINT chk_rating_kunjungan CHECK (rating BETWEEN 1 AND 5)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_kunjungan');
    }
};
