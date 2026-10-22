<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'value',
        'type',
        'description',
    ];

    /**
     * Klucze, które muszą być szyfrowane w bazie danych.
     */
    public static array $secretKeys = [
        'tpay_client_secret',
        'tpay_merchant_secret',
    ];

    /**
     * Sprawdza, czy dany klucz wymaga szyfrowania.
     */
    public static function isSecretKey(string $key): bool
    {
        return in_array($key, static::$secretKeys, true) || str_contains($key, 'secret') || str_contains($key, 'password');
    }

    /**
     * Pobiera wartość danego ustawienia ze wsparciem dla cache.
     * Dla kluczy secret automatycznie deszyfruje wartość.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (static::isSecretKey($key)) {
            return static::getSecret($key, $default);
        }

        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }

    /**
     * Bezpieczne pobranie wartości tajnej (odszyfrowanej).
     */
    public static function getSecret(string $key, ?string $default = null): ?string
    {
        $raw = Cache::remember("setting_raw_{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        if (empty($raw)) {
            return $default;
        }

        try {
            return Crypt::decryptString($raw);
        } catch (DecryptException) {
            // Wartość nie była jeszcze zaszyfrowana (plaintext)
            return $raw;
        }
    }

    /**
     * Zapisuje lub aktualizuje wartość ustawienia i czyści cache.
     */
    public static function set(string $key, mixed $value, ?string $label = null, ?string $type = 'text', ?string $description = null): self
    {
        $setting = static::firstOrNew(['key' => $key]);

        if (static::isSecretKey($key)) {
            if (!empty($value)) {
                $setting->value = Crypt::encryptString((string) $value);
            }
            $setting->type = 'password';
        } else {
            $setting->value = $value !== null ? (string) $value : null;
            if ($type && (!$setting->exists || empty($setting->type))) {
                $setting->type = $type;
            }
        }

        if ($label && (!$setting->exists || empty($setting->label))) {
            $setting->label = $label;
        }

        if ($description && (!$setting->exists || empty($setting->description))) {
            $setting->description = $description;
        }

        $setting->save();
        static::clearCacheFor($key);

        return $setting;
    }

    /**
     * Sprawdza, czy globalna konfiguracja Tpay jest kompletna.
     */
    public static function isTpayConfigured(): bool
    {
        $clientId = static::get('tpay_client_id');
        $clientSecret = static::getSecret('tpay_client_secret');

        return !empty($clientId) && !empty($clientSecret);
    }

    /**
     * Czyści cache dla danego klucza.
     */
    public static function clearCacheFor(string $key): void
    {
        Cache::forget("setting_{$key}");
        Cache::forget("setting_raw_{$key}");
    }

    /**
     * Czyści cache po aktualizacji modelu.
     */
    protected static function booted(): void
    {
        static::saved(function (Setting $setting) {
            static::clearCacheFor($setting->key);
        });

        static::deleted(function (Setting $setting) {
            static::clearCacheFor($setting->key);
        });
    }
}
