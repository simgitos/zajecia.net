<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_user', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('terms_accepted_at');
        });

        // Wypełnienie dotychczasowych wpisów w tabeli pivot rolami użytkowników
        $pivots = DB::table('school_user')->get();
        foreach ($pivots as $pivot) {
            $user = DB::table('users')->where('id', $pivot->user_id)->first();
            $roles = $user?->roles ?? json_encode(['user']);

            DB::table('school_user')
                ->where('id', $pivot->id)
                ->update(['roles' => $roles]);
        }
    }

    public function down(): void
    {
        Schema::table('school_user', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
