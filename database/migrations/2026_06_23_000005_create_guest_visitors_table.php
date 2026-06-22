<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('kode_guest')->unique();
            $table->string('session_id')->nullable()->index();
            $table->string('nama_opsional')->nullable();
            $table->string('asal_kota')->nullable();
            $table->date('tanggal_akses')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_visitors');
    }
};
