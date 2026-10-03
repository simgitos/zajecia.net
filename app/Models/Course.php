<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    protected $fillable = [
        'school_id',
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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_per_unit' => 'decimal:2',
        'max_participants' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
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
        return $this->belongsToMany(Child::class, 'child_course')->withTimestamps();
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
