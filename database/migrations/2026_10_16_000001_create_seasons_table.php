<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('season_id')->nullable()->after('school_id')->constrained('seasons')->nullOnDelete();
        });

        // Utworzenie domyślnego aktywnego sezonu dla istniejących szkół i przypisanie do nich istniejących kursów
        $schools = DB::table('schools')->get();
        $now = now();
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        if ($currentMonth >= 8) {
            $seasonName = "Rok szkolny {$currentYear}/" . ($currentYear + 1);
            $startDate = "{$currentYear}-09-01";
            $endDate = ($currentYear + 1) . "-06-30";
        } else {
            $seasonName = "Rok szkolny " . ($currentYear - 1) . "/{$currentYear}";
            $startDate = ($currentYear - 1) . "-09-01";
            $endDate = "{$currentYear}-06-30";
        }

        foreach ($schools as $school) {
            $seasonId = DB::table('seasons')->insertGetId([
                'school_id' => $school->id,
                'name' => $seasonName,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Przypisanie dotychczasowych kursów szkoły do nowo utworzonego sezonu
            DB::table('courses')
                ->where('school_id', $school->id)
                ->whereNull('season_id')
                ->update(['season_id' => $seasonId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['season_id']);
            $table->dropColumn('season_id');
        });

        Schema::dropIfExists('seasons');
    }
};
