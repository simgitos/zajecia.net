<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'nip',
        'email',
        'phone',
        'bank_account_number',
        'subscription_plan',
        'cancellation_cutoff_hours',
        'status',
        'tpay_client_id',
        'tpay_client_secret',
        'tpay_merchant_secret',
        'tpay_production_mode',
    ];

    protected function casts(): array
    {
        return [
            'tpay_production_mode' => 'boolean',
        ];
    }

    /**
     * Odszyfrowuje tpay_client_secret lub zwraca wartość bezpośrednio, jeśli została wpisana jako plaintext.
     */
    public function getTpayClientSecretAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
            return $value;
        }
    }

    /**
     * Zawsze szyfruje tpay_client_secret przed zapisem do bazy.
     */
    public function setTpayClientSecretAttribute(?string $value): void
    {
        $this->attributes['tpay_client_secret'] = !empty($value)
            ? \Illuminate\Support\Facades\Crypt::encryptString($value)
            : null;
    }

    /**
     * Odszyfrowuje tpay_merchant_secret lub zwraca wartość bezpośrednio, jeśli została wpisana jako plaintext.
     */
    public function getTpayMerchantSecretAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
            return $value;
        }
    }

    /**
     * Zawsze szyfruje tpay_merchant_secret przed zapisem do bazy.
     */
    public function setTpayMerchantSecretAttribute(?string $value): void
    {
        $this->attributes['tpay_merchant_secret'] = !empty($value)
            ? \Illuminate\Support\Facades\Crypt::encryptString($value)
            : null;
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'school_user')
            ->withPivot([
                'terms_accepted_at',
                'terms_accepted_ip',
                'rodo_accepted_at',
                'rodo_accepted_ip',
                'image_consent_accepted_at',
                'marketing_consent_accepted_at',
                'roles',
            ])
            ->withTimestamps();
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function seasons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * Zwraca aktualnie aktywny sezon szkoły.
     */
    public function activeSeason(): ?Season
    {
        return $this->seasons()->where('is_active', true)->first()
            ?? $this->seasons()->latest('id')->first();
    }
}
