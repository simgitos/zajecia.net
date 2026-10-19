<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    ];

    /**
     * Rzutowanie atrybutów modelu.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Akcesor zwracający ID aktualnie aktywnej szkoły.
     */
    public function getSchoolIdAttribute(): ?int
    {
        return $this->activeSchool()?->id;
    }

    /**
     * Akcesor zwracający instancję aktualnie aktywnej szkoły (kompatybilność z dawnym $user->school).
     */
    public function getSchoolAttribute(): ?School
    {
        return $this->activeSchool();
    }

    /**
     * Zwraca role użytkownika w danej szkole (z pivota school_user).
     */
    public function rolesForSchool(School|int $school): Collection
    {
        $schoolId = $school instanceof School ? $school->id : (int) $school;
        
        $membership = $this->relationLoaded('schools')
            ? $this->schools->firstWhere('id', $schoolId)
            : $this->schools()->where('schools.id', $schoolId)->first();

        if ($membership && $membership->pivot && $membership->pivot->roles) {
            $rawRoles = is_array($membership->pivot->roles)
                ? $membership->pivot->roles
                : json_decode((string) $membership->pivot->roles, true);

            if (is_array($rawRoles)) {
                return collect($rawRoles)->map(
                    fn ($r) => $r instanceof UserRole ? $r : UserRole::tryFrom((string) $r)
                )->filter()->values();
            }
        }

        return collect([UserRole::USER]);
    }

    /**
     * Zwraca role użytkownika w aktualnie aktywnej szkole.
     */
    public function rolesForActiveSchool(): Collection
    {
        $activeSchool = $this->activeSchool();
        if ($activeSchool) {
            return $this->rolesForSchool($activeSchool);
        }

        return collect([UserRole::USER]);
    }

    /**
     * Sprawdza czy użytkownik posiada wybraną rolę w aktualnie aktywnej szkole (lub przynajmniej jedną z podanych).
     *
     * @param UserRole|string|array<UserRole|string> $roles
     */
    public function hasRole(UserRole|string|array $roles): bool
    {
        $currentRoles = $this->rolesForActiveSchool();
        if ($currentRoles->isEmpty()) {
            return false;
        }

        $rolesArray = is_array($roles) ? $roles : [$roles];
        $enumRoles = array_filter(array_map(
            fn ($r) => $r instanceof UserRole ? $r : UserRole::tryFrom((string) $r),
            $rolesArray
        ));

        return $currentRoles->contains(
            fn (UserRole $role) => in_array($role, $enumRoles, true)
        );
    }

    /**
     * Zwraca aktualnie wybraną aktywną rolę z sesji lub domyślną o najwyższym priorytecie dla aktywnej szkoły.
     */
    public function activeRole(): ?UserRole
    {
        $currentRoles = $this->rolesForActiveSchool();
        if ($currentRoles->isEmpty()) {
            return null;
        }

        $sessionRole = session('active_role');
        if ($sessionRole) {
            $enumRole = $sessionRole instanceof UserRole ? $sessionRole : UserRole::tryFrom((string) $sessionRole);
            if ($enumRole && $this->hasRole($enumRole)) {
                return $enumRole;
            }
        }

        return $currentRoles->sortByDesc(fn (UserRole $role) => $role->priority())->first();
    }

    /**
     * Sprawdza czy użytkownik ma przypisanych więcej niż jedną rolę w aktywnej szkole.
     */
    public function hasMultipleRoles(): bool
    {
        return $this->rolesForActiveSchool()->count() > 1;
    }

    public function schools(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(School::class, 'school_user')
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

    /**
     * Zwraca aktualnie wybraną aktywną szkołę z sesji lub pierwszą przypisaną użytkownikowi.
     */
    public function activeSchool(): ?School
    {
        $activeSchoolId = session('active_school_id');

        if ($activeSchoolId) {
            $school = $this->relationLoaded('schools')
                ? $this->schools->firstWhere('id', (int) $activeSchoolId)
                : $this->schools()->where('schools.id', (int) $activeSchoolId)->first();

            if ($school) {
                return $school;
            }
        }

        return $this->relationLoaded('schools')
            ? $this->schools->first()
            : $this->schools()->first();
    }

    /**
     * Sprawdza, czy użytkownik zaakceptował regulamin oraz RODO dla aktualnie aktywnej szkoły.
     */
    public function hasAcceptedTermsForActiveSchool(): bool
    {
        $activeSchool = $this->activeSchool();
        if (!$activeSchool) {
            return true;
        }

        $membership = $this->schools()->where('schools.id', $activeSchool->id)->first();
        if (!$membership) {
            return true;
        }

        return $membership->pivot->terms_accepted_at !== null && $membership->pivot->rodo_accepted_at !== null;
    }

    /**
     * Zwraca rekord pivot ze zgodami dla aktualnie aktywnej szkoły.
     */
    public function activeSchoolMembership()
    {
        $activeSchool = $this->activeSchool();
        if (!$activeSchool) {
            return null;
        }

        return $this->schools()->where('schools.id', $activeSchool->id)->first();
    }

    /**
     * Sprawdza czy użytkownik jest przypisany do więcej niż jednej placówki.
     */
    public function hasMultipleSchools(): bool
    {
        return $this->schools()->count() > 1;
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Child::class, 'parent_id');
    }
}