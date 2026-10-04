<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'parent_id',
        'child_id',
        'course_id',
        'year_month',
        'billing_type',
        'price_per_unit',
        'realized_lessons_count',
        'present_count',
        'excused_count',
        'absent_count',
        'amount',
        'status',
        'paid_at',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price_per_unit' => 'decimal:2',
            'amount' => 'decimal:2',
            'realized_lessons_count' => 'integer',
            'present_count' => 'integer',
            'excused_count' => 'integer',
            'absent_count' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }
}
