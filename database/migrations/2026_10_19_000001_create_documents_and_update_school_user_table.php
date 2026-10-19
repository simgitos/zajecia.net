<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela dokumentów placówki
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->string('type'); // terms, privacy_policy, rodo, child_protection, other
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Dodatkowe pola zgód i IP w tabeli łączącej school_user
        Schema::table('school_user', function (Blueprint $table) {
            $table->string('terms_accepted_ip', 45)->nullable()->after('terms_accepted_at');
            $table->timestamp('rodo_accepted_at')->nullable()->after('terms_accepted_ip');
            $table->string('rodo_accepted_ip', 45)->nullable()->after('rodo_accepted_at');
            $table->timestamp('image_consent_accepted_at')->nullable()->after('rodo_accepted_ip');
            $table->timestamp('marketing_consent_accepted_at')->nullable()->after('image_consent_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('school_user', function (Blueprint $table) {
            $table->dropColumn([
                'terms_accepted_ip',
                'rodo_accepted_at',
                'rodo_accepted_ip',
                'image_consent_accepted_at',
                'marketing_consent_accepted_at',
            ]);
        });

        Schema::dropIfExists('documents');
    }
};
