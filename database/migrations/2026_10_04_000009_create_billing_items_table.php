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
        Schema::create('billing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('year_month', 7); // Format: YYYY-MM
            $table->string('billing_type'); // monthly_flat, per_lesson_monthly, per_lesson_single
            $table->decimal('price_per_unit', 8, 2);
            $table->integer('realized_lessons_count')->default(0);
            $table->integer('present_count')->default(0);
            $table->integer('excused_count')->default(0);
            $table->integer('absent_count')->default(0);
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable(); // cash, transfer, online
            $table->string('transaction_id')->nullable(); // dla płatności online
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['child_id', 'course_id', 'year_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_items');
    }
};
