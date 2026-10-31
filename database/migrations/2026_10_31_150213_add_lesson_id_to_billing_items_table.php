<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            if (!Schema::hasColumn('billing_items', 'lesson_id')) {
                $table->foreignId('lesson_id')->nullable()->after('course_id')->constrained('lessons')->nullOnDelete();
            }
            // MySQL wymaga indeksu dla klucza obcego child_id zanim usuniemy indeks unikalny
            $table->index('child_id');
            // Usuwamy stary indeks unikalny obejmujący [child_id, course_id, year_month]
            $table->dropUnique(['child_id', 'course_id', 'year_month']);
            // Dodajemy unikalność dla pozycji per lekcja (dla pojedynczego dziecka i lekcji)
            $table->unique(['child_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            $table->dropUnique(['child_id', 'lesson_id']);
            $table->dropForeign(['lesson_id']);
            $table->dropColumn('lesson_id');
            $table->unique(['child_id', 'course_id', 'year_month']);
        });
    }
};
