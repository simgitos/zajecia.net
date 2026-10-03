<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'school_id',
        'topic',
        'notes',
        'realized_at',
    ];

    protected function casts(): array
    {
        return [
            'realized_at' => 'date',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(LessonAttendance::class);
    }

    /**
     * Liczba obecnych dzieci na danej lekcji.
     */
    public function presentCount(): int
    {
        return $this->attendances()->where('status', 'present')->count();
    }
}
