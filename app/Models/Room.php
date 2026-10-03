<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'location',
        'capacity',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
