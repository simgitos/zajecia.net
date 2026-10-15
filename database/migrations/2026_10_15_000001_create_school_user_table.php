<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'user_id']);
        });

        // Przepisanie dotychczasowych powiązań z users.school_id do tabeli pivot
        $existingUsers = DB::table('users')->whereNotNull('school_id')->get();
        $now = now();
        $insertData = [];

        foreach ($existingUsers as $user) {
            $insertData[] = [
                'school_id' => $user->school_id,
                'user_id' => $user->id,
                'terms_accepted_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($insertData)) {
            DB::table('school_user')->insertOrIgnore($insertData);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('school_user');
    }
};
