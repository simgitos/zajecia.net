<?php

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
        // Bezpieczna zmiana typu kolumny subscription_plan na VARCHAR(50) w MySQL
        DB::statement("ALTER TABLE `schools` MODIFY `subscription_plan` VARCHAR(50) NOT NULL DEFAULT 'solo'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `schools` MODIFY `subscription_plan` ENUM('micro', 'standard', 'pro', 'enterprise') NOT NULL DEFAULT 'micro'");
    }
};
