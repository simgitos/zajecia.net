<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'school_id',
        'title',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'file_size' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, DocumentType|string $type): Builder
    {
        $val = $type instanceof DocumentType ? $type->value : $type;
        return $query->where('type', $val);
    }

    public function formattedSize(): string
    {
        if (!$this->file_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $this->file_size > 0 ? floor(log($this->file_size, 1024)) : 0;
        return number_format($this->file_size / pow(1024, $power), 1, ',', ' ') . ' ' . ($units[$power] ?? 'B');
    }

    public function fileUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
