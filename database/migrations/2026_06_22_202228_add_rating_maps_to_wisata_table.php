<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('wisata') || Schema::hasColumn('wisata', 'rating_maps')) {
            return;
        }

        Schema::table('wisata', function (Blueprint $table) {
            $table->decimal('rating_maps', 2, 1)->nullable()->after('total_estimasi_biaya');
            $table->unsignedInteger('jumlah_rating_maps')->default(0)->after('rating_maps');
            $table->timestamp('rating_maps_updated_at')->nullable()->after('jumlah_rating_maps');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('wisata') || ! Schema::hasColumn('wisata', 'rating_maps')) {
            return;
        }

        Schema::table('wisata', function (Blueprint $table) {
            $table->dropColumn([
                'rating_maps',
                'jumlah_rating_maps',
                'rating_maps_updated_at',
            ]);
        });
    }
};
