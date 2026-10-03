<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Child;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseEnrollmentService
{
    /**
     * Zapisuje dziecko na wybrane zajęcia w ramach bieżącej szkoły.
     *
     * @throws ValidationException
     */
    public function enroll(Child $child, Course $course, User $parent): void
    {
        // 1. Weryfikacja właściciela dziecka
        if ($child->parent_id !== $parent->id) {
            throw ValidationException::withMessages([
                'child_id' => ['To dziecko nie należy do Twojego konta.'],
            ]);
        }

        // 2. Weryfikacja przynależności do tej samej szkoły
        if ($child->school_id !== $parent->school_id || $course->school_id !== $parent->school_id) {
            throw ValidationException::withMessages([
                'course_id' => ['Zajęcia oraz dziecko muszą należeć do Twojej szkoły.'],
            ]);
        }

        // 3. Sprawdzenie czy zajęcia są aktywne
        if (!$course->is_active) {
            throw ValidationException::withMessages([
                'course_id' => ['Nie można zapisać dziecka na nieaktywne zajęcia.'],
            ]);
        }

        // 4. Bezpieczny zapis z blokadą w transakcji bazy danych (pessimistic lock)
        DB::transaction(function () use ($child, $course) {
            // Blokada wiersza kursu, zapobiegająca wyścigom (race condition) przy braku miejsc
            $lockedCourse = Course::where('id', $course->id)->lockForUpdate()->firstOrFail();

            // Sprawdzenie czy dziecko jest już zapisane
            if ($child->courses()->where('course_id', $lockedCourse->id)->exists()) {
                throw ValidationException::withMessages([
                    'course_id' => ['Dziecko jest już zapisane na te zajęcia.'],
                ]);
            }

            // Sprawdzenie dostępnych miejsc
            $currentCount = $lockedCourse->children()->count();
            if ($currentCount >= $lockedCourse->max_participants) {
                throw ValidationException::withMessages([
                    'course_id' => ["Brak wolnych miejsc na zajęciach \"{$lockedCourse->title}\" (Limit: {$lockedCourse->max_participants})."],
                ]);
            }

            // Przypisanie dziecka do zajęć
            $child->courses()->attach($lockedCourse->id);
        });
    }

    /**
     * Wypisuje dziecko z zajęć.
     *
     * @throws ValidationException
     */
    public function unenroll(Child $child, Course $course, User $parent): void
    {
        if ($child->parent_id !== $parent->id) {
            throw ValidationException::withMessages([
                'child_id' => ['To dziecko nie należy do Twojego konta.'],
            ]);
        }

        if ($child->school_id !== $parent->school_id || $course->school_id !== $parent->school_id) {
            throw ValidationException::withMessages([
                'course_id' => ['Brak uprawnień do operacji na tym kursie.'],
            ]);
        }

        $child->courses()->detach($course->id);
    }
}
