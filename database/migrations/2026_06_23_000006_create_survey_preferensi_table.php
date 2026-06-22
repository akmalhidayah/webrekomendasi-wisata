<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_preferensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_visitor_id')->index()->constrained('guest_visitors')->cascadeOnDelete();
            $table->foreignId('wisata_id')->index()->constrained('wisata')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating_awal');
            $table->timestamps();

            $table->unique(['guest_visitor_id', 'wisata_id']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE survey_preferensi ADD CONSTRAINT chk_survey_rating_awal CHECK (rating_awal BETWEEN 1 AND 5)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_preferensi');
    }
};
