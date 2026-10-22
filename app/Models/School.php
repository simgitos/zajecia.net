<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'name',
        'full_name',
        'slug',
        'nip',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'bank_account_number',
        'subscription_plan',
        'cancellation_cutoff_hours',
        'status',
        'tpay_client_id',
        'tpay_client_secret',
        'tpay_merchant_secret',
        'tpay_production_mode',
        'tpay_whitelist_status',
        'tpay_whitelist_at',
        'tpay_whitelist_response',
    ];

    protected function casts(): array
    {
        return [
            'tpay_production_mode' => 'boolean',
            'tpay_whitelist_at' => 'datetime',
        ];
    }

    /**
     * Zwraca numer konta sformatowany w formacie IBAN (np. PL + 26 cyfr) dla Tpay Collect.
     */
    public function formattedIban(): ?string
    {
        if (empty($this->bank_account_number)) {
            return null;
        }

        $clean = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->bank_account_number);
        if (empty($clean)) {
            return null;
        }

        if (!str_starts_with(strtoupper($clean), 'PL')) {
            $clean = 'PL' . $clean;
        }

        return strtoupper($clean);
    }

    /**
     * Sprawdza, czy placówka ma zweryfikowane konto w białej liście Tpay.
     */
    public function isTpayWhitelisted(): bool
    {
        return $this->tpay_whitelist_status === 'verified';
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

    public function courses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Child::class);
    }

    public function rooms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Room::class);
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
