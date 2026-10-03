<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['group', 'individual'])->default('group');
            $table->enum('billing_type', ['monthly_flat', 'per_lesson_monthly', 'per_lesson_single'])->default('monthly_flat');
            $table->decimal('price_per_unit', 8, 2);
            $table->integer('max_participants')->default(15);
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
