<?php

declare(strict_types=1);

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
        Schema::create('lesson_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->enum('status', ['present', 'absent', 'excused'])->default('present');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'child_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_attendances');
    }
};
