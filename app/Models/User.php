<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleType;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Traits\Filament\AppAuthenticationRecoveryCodes;
use App\Traits\Filament\AppAuthenticationSecret;
use App\Traits\UuidTrait;
use Carbon\CarbonImmutable;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use LaravelDaily\FilaTeams\Concerns\HasTeams;
use LaravelDaily\FilaTeams\Contracts\HasTeamMembership;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_suspended
 * @property CarbonImmutable|null $suspended_at
 * @property bool $is_approved
 * @property int|null $approved_by
 * @property string|null $suspension_reason
 * @property string|null $app_authentication_secret
 * @property array<array-key, mixed>|null $app_authentication_recovery_codes
 * @property string|null $theme_color
 * @property string|null $locale
 * @property string|null $custom_fields
 * @property string|null $remember_token
 * @property CarbonImmutable|null $email_verified_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property CarbonImmutable|null $last_login_at
 * @property string|null $last_login_ip
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppAuthenticationRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppAuthenticationSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCustomFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsSuspended($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspensionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereThemeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method bool isManagerOfTeam(Team $team)
 * @method bool isStudentOfTeam(Team $team)
 * @method \Illuminate\Support\Collection getRolesForTeam(Team $team)
 * @method bool hasAnyRoleInTeam(Team $team)
 * @method bool hasManagerRoleInAnyTeam()
 * @method void assignRoleInTeam(Role $role, Team $team)
 * @method void removeRoleFromTeam(string $roleName, Team $team)
 * @method void removeAllRolesFromTeam(Team $team)
 * @method \Illuminate\Database\Eloquent\Relations\MorphToMany<\Spatie\Permission\Models\Role, \App\Models\User> rolesWithTeams()
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'uuid',
    'name',
    'email',
    'password',
    'email_verified_at',
    'is_suspended',
    'suspended_at',
    'suspension_reason',
    'is_approved',
    'approved_by',
    'remember_token',
])]
#[Hidden([
    'password',
    'app_authentication_secret',
    'app_authentication_recovery_codes',
    'remember_token',
])]
class User extends Authenticatable implements AuditableContract, FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasMedia, HasTeamMembership, MustVerifyEmail
{
    use AppAuthenticationRecoveryCodes;
    use AppAuthenticationSecret;
    use HasFactory;
    use HasRoles;
    use HasTeams;
    use InteractsWithMedia;
    use Notifiable;
    use UuidTrait;
    use \OwenIt\Auditing\Auditable;

    private ?Collection $cachedTenants = null;

    // ==========================================
    // Setup & Configuration
    // ==========================================

    #[\Override]
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'is_approved' => 'boolean',
            'created_at' => 'datetime:d/m/Y H:i',
            'updated_at' => 'datetime:d/m/Y H:i',
            'email_verified_at' => 'datetime:d/m/Y H:i',
            'suspended_at' => 'datetime:d/m/Y H:i',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('s3')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void {}

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return MorphToMany<Role, $this, MorphPivot>
     */
    public function rolesWithTeams(): MorphToMany
    {
        return $this->morphToMany(
            Role::class,
            'model',
            config('permission.table_names.model_has_roles', 'model_has_roles'),
            'model_id',
            'role_id'
        )->withPivot('team_id');
    }

    public function authentications()
    {
        return $this->audits()->whereIn('event', ['login', 'logout', 'failed_login'])->latest();
    }

    public function latestAuthentication()
    {
        return $this->audits()->whereIn('event', ['login', 'logout', 'failed_login'])->latest()->first();
    }

    // ==========================================
    // Scopes
    // ==========================================

    #[Scope]
    protected function withRolesForTeam($query, Team $team): void
    {
        $query->with([
            'rolesWithTeams' => fn ($q) => $q->where('model_has_roles.team_id', $team->id),
        ]);
    }

    // ==========================================
    // Filament / Access Control
    // ==========================================

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'auth') {
            return true;
        }

        if ($this->isSuspended()) {
            return false;
        }

        if (! $this->hasVerifiedEmail()) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->hasRole(RoleType::ADMIN->value);
        }

        $roleRequired = match ($panel->getId()) {
            'manager' => RoleType::MANAGER->value,
            'tae' => RoleType::TAE->value,
            'teacher' => RoleType::TEACHER->value,
            'student' => RoleType::STUDENT->value,
            default => null,
        };

        if ($roleRequired) {
            return $this->rolesWithTeams()->where('roles.name', $roleRequired)->exists();
        }

        return false;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (! $tenant instanceof Team) {
            return false;
        }

        if (! $tenant->is_active) {
            return false;
        }

        return $this->teams()->whereKey($tenant->getKey())->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        if ($this->cachedTenants instanceof Collection) {
            return $this->cachedTenants;
        }

        return $this->cachedTenants = $this->teams()->where('is_active', true)->get();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $media = $this->getFirstMedia('avatar');

        if ($media instanceof Media) {
            try {
                return $media->getUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    // ==========================================
    // State Checks & Notifications
    // ==========================================

    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    #[\Override]
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // ==========================================
    // Team & Role Logic (Business Logic)
    // ==========================================

    public function isManagerOfTeam(Team $team): bool
    {
        return $this->getRoleQueryBuilder($team)
            ->where('roles.name', RoleType::MANAGER->value)
            ->exists();
    }

    public function isStudentOfTeam(Team $team): bool
    {
        return $this->getRoleQueryBuilder($team)
            ->where('roles.name', RoleType::STUDENT->value)
            ->exists();
    }

    public function hasAnyRoleInTeam(Team $team): bool
    {
        return $this->getRoleQueryBuilder($team)->exists();
    }

    public function hasManagerRoleInAnyTeam(): bool
    {
        return $this->rolesWithTeams()
            ->where('roles.name', RoleType::MANAGER->value)
            ->exists();
    }

    public function getRolesForTeam(Team $team): Collection
    {
        return $this->getRoleQueryBuilder($team)
            ->select('roles.*')
            ->get();
    }

    public function assignRoleInTeam(Role $role, Team $team): void
    {
        $this->rolesWithTeams()->syncWithoutDetaching([
            $role->getKey() => ['team_id' => $team->id],
        ]);
    }

    public function removeRoleFromTeam(string $roleName, Team $team): void
    {
        $role = Role::query()
            ->where('name', $roleName)
            ->where('team_id', $team->id)
            ->first();

        if (! $role) {
            return;
        }

        $this->rolesWithTeams()
            ->wherePivot('team_id', $team->id)
            ->detach($role->getKey());
    }

    public function removeAllRolesFromTeam(Team $team): void
    {
        $this->rolesWithTeams()
            ->wherePivot('team_id', $team->id)
            ->detach();
    }

    private function getRoleQueryBuilder(Team $team): Builder
    {
        return Role::query()
            ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'roles.id')
            ->where('mhr.model_type', self::class)
            ->where('mhr.model_id', $this->id)
            ->where('mhr.team_id', $team->id);
    }

    public function auditEvent(string $event): void
    {
        \OwenIt\Auditing\Models\Audit::create([
            'user_type'      => self::class,
            'user_id'        => $this->id,
            'auditable_type' => self::class,
            'auditable_id'   => $this->id,
            'event'          => $event,
            'url'            => request()->fullUrl(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'tags'           => null,
            'old_values'     => null,
            'new_values'     => null,
            'created_at'     => now(),
        ]);
    }
}
