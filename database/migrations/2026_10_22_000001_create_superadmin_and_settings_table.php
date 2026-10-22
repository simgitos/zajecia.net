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
        // 1. Dodanie kolumny is_superadmin do tabeli users (jeśli jeszcze nie istnieje)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_superadmin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_superadmin')->default(false)->after('password');
            });
        }

        // 2. Utworzenie tabeli settings dla panelu SU
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('label');
                $table->text('value')->nullable();
                $table->string('type')->default('text'); // text, number, boolean, textarea
                $table->string('description')->nullable();
                $table->timestamps();
            });

            // Wstawienie domyślnych kluczowych ustawień
            DB::table('settings')->insert([
                [
                    'key' => 'online_payment_commission_percent',
                    'label' => 'Prowizja operatora płatności online (%)',
                    'value' => '1.5',
                    'type' => 'number',
                    'description' => 'Procentowa wartość prowizji doliczanej do transakcji online w systemie.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'free_trial_days',
                    'label' => 'Długość darmowego okresu próbnego (dni)',
                    'value' => '14',
                    'type' => 'number',
                    'description' => 'Liczba dni darmowego testu przy rejestracji nowej placówki.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'support_contact_email',
                    'label' => 'Główny e-mail wsparcia i kontaktu',
                    'value' => 'kontakt@zajeciownia.pl',
                    'type' => 'text',
                    'description' => 'Adres e-mail wyświetlany w stopce i powiadomieniach systemowych.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'system_notification_banner',
                    'label' => 'Komunikat globalny (pasek ogłoszeń)',
                    'value' => '',
                    'type' => 'textarea',
                    'description' => 'Opcjonalny komunikat widoczny na górze systemu dla wszystkich użytkowników (pozostaw puste, aby wyłączyć).',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_superadmin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_superadmin');
            });
        }

        Schema::dropIfExists('settings');
    }
};
