<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Etykieta słowna statusu sezonu.
     */
    public function statusLabel(): string
    {
        if ($this->is_active) {
            return 'Bieżący (aktywny)';
        }

        $today = now()->startOfDay();

        if ($this->start_date && $today->lt($this->start_date)) {
            return 'Planowany';
        }

        if ($this->end_date && $today->gt($this->end_date)) {
            return 'Zakończony';
        }

        return 'Nieaktywny';
    }

    /**
     * Kolor odznaki (badge) dla statusu Tabler UI.
     */
    public function statusBadgeColor(): string
    {
        if ($this->is_active) {
            return 'success';
        }

        $today = now()->startOfDay();

        if ($this->start_date && $today->lt($this->start_date)) {
            return 'warning';
        }

        if ($this->end_date && $today->gt($this->end_date)) {
            return 'secondary';
        }

        return 'azure';
    }

    /**
     * Sformatowany zakres dat (np. 01.09.2026 - 30.06.2027).
     */
    public function dateRangeFormatted(): string
    {
        if (!$this->start_date || !$this->end_date) {
            return '-';
        }

        return $this->start_date->format('d.m.Y') . ' – ' . $this->end_date->format('d.m.Y');
    }
}
