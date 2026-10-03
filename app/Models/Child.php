<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        'name',
        'birth_date',
        'pesel',
        'parent_comment',
        'notes',
        'consents',
        'school_id',
        'parent_id',
    ];

    /**
     * Rzutowanie atrybutów modelu.
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'pesel' => 'encrypted',
            'consents' => 'array',
        ];
    }

    /**
     * Rodzic (użytkownik), do którego przypisane jest dziecko.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Szkoła, do której uczęszcza dziecko.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Zajęcia/kursy, na które zapisane jest dziecko.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'child_course')->withTimestamps();
    }

    /**
     * Oblicza wiek dziecka w latach.
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birth_date ? Carbon::parse($this->birth_date)->age : null
        );
    }
}
