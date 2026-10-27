<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    protected $fillable = [
        'school_id',
        'season_id',
        'instructor_id',
        'room_id',
        'title',
        'type',
        'billing_type',
        'price_per_unit',
        'max_participants',
        'min_age',
        'max_age',
        'description',
        'schedule_info',
        'is_active',
        'schedule_day_of_week',
        'schedule_start_time',
        'schedule_end_time',
        'schedule_frequency',
        'slot_duration_minutes',
        'slot_break_minutes',
        'schedule_slots',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_per_unit' => 'decimal:2',
        'max_participants' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'schedule_day_of_week' => 'integer',
        'slot_duration_minutes' => 'integer',
        'slot_break_minutes' => 'integer',
        'schedule_slots' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'child_course')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order', 'asc');
    }

    public function isIndividual(): bool
    {
        return $this->type === 'individual';
    }

    public function isFull(): bool
    {
        return $this->children()->count() >= $this->max_participants;
    }

    public function availableSlots(): int
    {
        return max(0, $this->max_participants - $this->children()->count());
    }

    public function lessons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}

