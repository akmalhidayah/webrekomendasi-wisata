<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_rekomendasi', function (Blueprint $table) {
            if (! Schema::hasColumn('hasil_rekomendasi', 'hotel_id')) {
                $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
            }

            if (! Schema::hasColumn('hasil_rekomendasi', 'estimasi_biaya_wisata')) {
                $table->decimal('estimasi_biaya_wisata', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('hasil_rekomendasi', 'estimasi_biaya_hotel')) {
                $table->decimal('estimasi_biaya_hotel', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('hasil_rekomendasi', 'total_estimasi_budget')) {
                $table->decimal('total_estimasi_budget', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('hasil_rekomendasi', 'jarak_km')) {
                $table->decimal('jarak_km', 8, 2)->nullable();
            }

            foreach ([
                'skor_cf',
                'skor_budget',
                'skor_jarak',
                'skor_preferensi',
                'skor_rating_destinasi',
                'skor_akhir',
            ] as $column) {
                if (! Schema::hasColumn('hasil_rekomendasi', $column)) {
                    $table->decimal($column, 6, 4)->nullable();
                }
            }

            if (! Schema::hasColumn('hasil_rekomendasi', 'alasan_rekomendasi')) {
                $table->json('alasan_rekomendasi')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('hasil_rekomendasi', function (Blueprint $table) {
            if (Schema::hasColumn('hasil_rekomendasi', 'hotel_id')) {
                $table->dropConstrainedForeignId('hotel_id');
            }

            $columns = array_values(array_filter([
                Schema::hasColumn('hasil_rekomendasi', 'alasan_rekomendasi') ? 'alasan_rekomendasi' : null,
                Schema::hasColumn('hasil_rekomendasi', 'skor_akhir') ? 'skor_akhir' : null,
                Schema::hasColumn('hasil_rekomendasi', 'skor_rating_destinasi') ? 'skor_rating_destinasi' : null,
                Schema::hasColumn('hasil_rekomendasi', 'skor_preferensi') ? 'skor_preferensi' : null,
                Schema::hasColumn('hasil_rekomendasi', 'skor_jarak') ? 'skor_jarak' : null,
                Schema::hasColumn('hasil_rekomendasi', 'skor_budget') ? 'skor_budget' : null,
                Schema::hasColumn('hasil_rekomendasi', 'skor_cf') ? 'skor_cf' : null,
                Schema::hasColumn('hasil_rekomendasi', 'jarak_km') ? 'jarak_km' : null,
                Schema::hasColumn('hasil_rekomendasi', 'total_estimasi_budget') ? 'total_estimasi_budget' : null,
                Schema::hasColumn('hasil_rekomendasi', 'estimasi_biaya_hotel') ? 'estimasi_biaya_hotel' : null,
                Schema::hasColumn('hasil_rekomendasi', 'estimasi_biaya_wisata') ? 'estimasi_biaya_wisata' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
