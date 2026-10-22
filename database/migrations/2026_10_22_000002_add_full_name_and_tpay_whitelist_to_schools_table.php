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
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'full_name')) {
                $table->string('full_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('schools', 'tpay_whitelist_status')) {
                $table->string('tpay_whitelist_status')->default('unverified')->after('tpay_production_mode');
            }
            if (!Schema::hasColumn('schools', 'tpay_whitelist_at')) {
                $table->timestamp('tpay_whitelist_at')->nullable()->after('tpay_whitelist_status');
            }
            if (!Schema::hasColumn('schools', 'tpay_whitelist_response')) {
                $table->text('tpay_whitelist_response')->nullable()->after('tpay_whitelist_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'tpay_whitelist_status',
                'tpay_whitelist_at',
                'tpay_whitelist_response',
            ]);
        });
    }
};
