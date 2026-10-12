<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'password',
        'roles',
        'school_id',
    ];

    /**
     * Rzutowanie atrybutów modelu.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // Eloquent automatycznie zamienia JSON na Collection<UserRole>
            'roles' => AsEnumCollection::of(UserRole::class),
        ];
    }

    /**
     * Sprawdza czy użytkownik posiada wybraną rolę (lub przynajmniej jedną z podanych).
     *
     * @param UserRole|string|array<UserRole|string> $roles
     */
    public function hasRole(UserRole|string|array $roles): bool
    {
        if (!$this->roles) {
            return false;
        }

        $rolesArray = is_array($roles) ? $roles : [$roles];
        $enumRoles = array_filter(array_map(
            fn ($r) => $r instanceof UserRole ? $r : UserRole::tryFrom((string) $r),
            $rolesArray
        ));

        return $this->roles->contains(
            fn (UserRole $role) => in_array($role, $enumRoles, true)
        );
    }

    /**
     * Zwraca aktualnie wybraną aktywną rolę z sesji lub domyślną o najwyższym priorytecie.
     */
    public function activeRole(): ?UserRole
    {
        if (!$this->roles || $this->roles->isEmpty()) {
            return null;
        }

        $sessionRole = session('active_role');
        if ($sessionRole) {
            $enumRole = $sessionRole instanceof UserRole ? $sessionRole : UserRole::tryFrom((string) $sessionRole);
            if ($enumRole && $this->hasRole($enumRole)) {
                return $enumRole;
            }
        }

        return $this->roles->sortByDesc(fn (UserRole $role) => $role->priority())->first();
    }

    /**
     * Sprawdza czy użytkownik ma przypisanych więcej niż jedną rolę.
     */
    public function hasMultipleRoles(): bool
    {
        return $this->roles !== null && $this->roles->count() > 1;
    }

    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Child::class, 'parent_id');
    }
}