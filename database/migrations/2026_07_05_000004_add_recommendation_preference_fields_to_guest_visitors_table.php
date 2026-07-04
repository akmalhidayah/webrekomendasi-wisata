<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_visitors', function (Blueprint $table) {
            if (! Schema::hasColumn('guest_visitors', 'budget_min')) {
                $table->decimal('budget_min', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('guest_visitors', 'budget_max')) {
                $table->decimal('budget_max', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('guest_visitors', 'butuh_hotel')) {
                $table->boolean('butuh_hotel')->default(false);
            }

            if (! Schema::hasColumn('guest_visitors', 'jumlah_malam')) {
                $table->unsignedTinyInteger('jumlah_malam')->default(1);
            }

            if (! Schema::hasColumn('guest_visitors', 'user_latitude')) {
                $table->decimal('user_latitude', 10, 7)->nullable();
            }

            if (! Schema::hasColumn('guest_visitors', 'user_longitude')) {
                $table->decimal('user_longitude', 10, 7)->nullable();
            }

            if (! Schema::hasColumn('guest_visitors', 'is_location_allowed')) {
                $table->boolean('is_location_allowed')->default(false);
            }

            if (! Schema::hasColumn('guest_visitors', 'location_captured_at')) {
                $table->timestamp('location_captured_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('guest_visitors', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('guest_visitors', 'location_captured_at') ? 'location_captured_at' : null,
                Schema::hasColumn('guest_visitors', 'is_location_allowed') ? 'is_location_allowed' : null,
                Schema::hasColumn('guest_visitors', 'user_longitude') ? 'user_longitude' : null,
                Schema::hasColumn('guest_visitors', 'user_latitude') ? 'user_latitude' : null,
                Schema::hasColumn('guest_visitors', 'jumlah_malam') ? 'jumlah_malam' : null,
                Schema::hasColumn('guest_visitors', 'butuh_hotel') ? 'butuh_hotel' : null,
                Schema::hasColumn('guest_visitors', 'budget_max') ? 'budget_max' : null,
                Schema::hasColumn('guest_visitors', 'budget_min') ? 'budget_min' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
