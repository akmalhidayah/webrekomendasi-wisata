<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wisata', function (Blueprint $table) {
            if (! Schema::hasColumn('wisata', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('alamat');
            }

            if (! Schema::hasColumn('wisata', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }

            if (! Schema::hasColumn('wisata', 'maps_url')) {
                $table->text('maps_url')->nullable()->after('longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wisata', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('wisata', 'maps_url') ? 'maps_url' : null,
                Schema::hasColumn('wisata', 'longitude') ? 'longitude' : null,
                Schema::hasColumn('wisata', 'latitude') ? 'latitude' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
