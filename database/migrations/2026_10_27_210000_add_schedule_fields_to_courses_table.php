<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('schedule_day_of_week')->nullable()->after('schedule_info')
                ->comment('Dzień tygodnia: 1=Pon, 2=Wt, 3=Śr, 4=Czw, 5=Pt, 6=Sob, 7=Ndz');
            $table->time('schedule_start_time')->nullable()->after('schedule_day_of_week')
                ->comment('Godzina rozpoczęcia zajęć');
            $table->time('schedule_end_time')->nullable()->after('schedule_start_time')
                ->comment('Godzina zakończenia zajęć');
            $table->enum('schedule_frequency', ['weekly', 'biweekly', 'once'])->nullable()->after('schedule_end_time')
                ->comment('Częstotliwość: co tydzień, co 2 tygodnie, jednorazowo');
            $table->unsignedSmallInteger('slot_duration_minutes')->nullable()->after('schedule_frequency')
                ->comment('Czas trwania slotu w minutach (dla indywidualnych)');
            $table->unsignedSmallInteger('slot_break_minutes')->nullable()->after('slot_duration_minutes')
                ->comment('Przerwa techniczna między slotami w minutach');
            $table->json('schedule_slots')->nullable()->after('slot_break_minutes')
                ->comment('Wygenerowane sloty indywidualne [{start_time, end_time, is_active}]');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'schedule_day_of_week',
                'schedule_start_time',
                'schedule_end_time',
                'schedule_frequency',
                'slot_duration_minutes',
                'slot_break_minutes',
                'schedule_slots',
            ]);
        });
    }
};
