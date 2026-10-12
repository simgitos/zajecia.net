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
            $table->string('tpay_client_id')->nullable()->after('status');
            $table->text('tpay_client_secret')->nullable()->after('tpay_client_id');
            $table->text('tpay_merchant_secret')->nullable()->after('tpay_client_secret');
            $table->boolean('tpay_production_mode')->default(false)->after('tpay_merchant_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'tpay_client_id',
                'tpay_client_secret',
                'tpay_merchant_secret',
                'tpay_production_mode',
            ]);
        });
    }
};
