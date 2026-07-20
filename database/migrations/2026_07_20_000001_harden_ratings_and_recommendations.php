<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rating_kunjungan', function (Blueprint $table) {
            $table->string('status', 20)->default('approved')->change();
        });

        DB::transaction(function () {
            DB::table('rating_kunjungan')->where('status', 'disetujui')->update(['status' => 'approved']);

            DB::table('rating_kunjungan')->whereNotNull('guest_visitor_id')
                ->orderByDesc('updated_at')->orderByDesc('id')->get()
                ->groupBy(fn ($row) => $row->guest_visitor_id.'-'.$row->wisata_id)
                ->each(function ($rows) {
                    $keep = $rows->first();
                    if (blank($keep->ulasan)) {
                        $comment = $rows->first(fn ($row) => filled($row->ulasan))?->ulasan;
                        if ($comment) {
                            DB::table('rating_kunjungan')->where('id', $keep->id)->update(['ulasan' => $comment]);
                        }
                    }
                    DB::table('rating_kunjungan')->whereIn('id', $rows->skip(1)->pluck('id'))->delete();
                });

            DB::table('hasil_rekomendasi')->orderByDesc('updated_at')->orderByDesc('id')->get()
                ->groupBy('guest_visitor_id')->each(function ($rows) {
                    $unique = $rows->unique('wisata_id')->values();
                    DB::table('hasil_rekomendasi')->whereIn('id', $rows->pluck('id'))->delete();
                    foreach ($unique as $ranking => $row) {
                        $data = (array) $row;
                        unset($data['id']);
                        $data['ranking'] = $ranking + 1;
                        DB::table('hasil_rekomendasi')->insert($data);
                    }
                });
        });

        Schema::table('rating_kunjungan', function (Blueprint $table) {
            $table->unique(['guest_visitor_id', 'wisata_id'], 'rating_guest_wisata_unique');
            $table->index(['wisata_id', 'status'], 'rating_wisata_status_idx');
        });
        Schema::table('hasil_rekomendasi', function (Blueprint $table) {
            $table->unique(['guest_visitor_id', 'ranking'], 'hasil_guest_ranking_unique');
            $table->unique(['guest_visitor_id', 'wisata_id'], 'hasil_guest_wisata_unique');
        });
        Schema::table('wisata', fn (Blueprint $table) => $table->index(['status', 'deleted_at'], 'wisata_status_deleted_idx'));
    }

    public function down(): void
    {
        Schema::table('rating_kunjungan', function (Blueprint $table) {
            $table->dropUnique('rating_guest_wisata_unique');
            $table->dropIndex('rating_wisata_status_idx');
        });
        Schema::table('hasil_rekomendasi', function (Blueprint $table) {
            $table->dropUnique('hasil_guest_ranking_unique');
            $table->dropUnique('hasil_guest_wisata_unique');
        });
        Schema::table('wisata', fn (Blueprint $table) => $table->dropIndex('wisata_status_deleted_idx'));
    }
};
