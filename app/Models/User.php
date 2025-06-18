<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Interfaces\Attachable;
use App\Interfaces\HasRoles;
use App\Models\Traits\HasAttachments;
use App\Models\Traits\HasTaxonomies;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property UserStatus $status
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property array|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read DatabaseNotificationCollection $unreadNotifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @property-read Collection|Attachment[] $attachments
 * @property-read int|null $attachments_count
 */
class User extends Authenticatable implements MustVerifyEmail, HasRoles, Attachable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasAttachments, HasTaxonomies;

    protected $appends = [
        'avatar_url',
    ];

    public function getAvatarUrlAttribute(): string
    {
        $avatar = $this->attachments()->where('collection', 'avatar')->first();

        if ($avatar) {
            return $avatar->url;
        }

        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email)));
    }

    /**
     * Determine if two-factor authentication has been enabled.
     *
     * @return bool
     */
    public function hasTwoFactorEnabled(): bool
    {
        return ! is_null($this->two_factor_secret) && ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Determine if two-factor authentication has been confirmed.
     *
     * @return bool
     */
    public function hasConfirmedTwoFactor(): bool
    {
        return ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get the two-factor authentication QR code URL.
     *
     * @param string $companyName
     * @return string|null
     */
    public function twoFactorQrCodeSvg(string $companyName = 'TALL Boilerplate'): ?string
    {
        if (! $this->two_factor_secret) {
            return null;
        }

        $twoFactorService = app(\App\Services\TwoFactorAuthenticationService::class);

        return $twoFactorService->qrCodeSvg(
            $companyName,
            $this->email,
            decrypt($this->two_factor_secret)
        );
    }

    /**
     * Enable two-factor authentication for the user.
     *
     * @return void
     */
    public function enableTwoFactorAuthentication(): void
    {
        $twoFactorService = app(\App\Services\TwoFactorAuthenticationService::class);

        $this->two_factor_secret = encrypt($twoFactorService->generateSecretKey());
        $this->two_factor_recovery_codes = $twoFactorService->generateRecoveryCodes();
        $this->save();
    }

    /**
     * Confirm two-factor authentication for the user.
     *
     * @return void
     */
    public function confirmTwoFactorAuthentication(): void
    {
        $this->two_factor_confirmed_at = now();
        $this->save();
    }

    /**
     * Disable two-factor authentication for the user.
     *
     * @return void
     */
    public function disableTwoFactorAuthentication(): void
    {
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->two_factor_confirmed_at = null;
        $this->save();
    }

    /**
     * Validate the given two-factor authentication code.
     *
     * @param string $code
     * @return bool
     */
    public function validateTwoFactorCode(string $code): bool
    {
        if (! $this->two_factor_secret) {
            return false;
        }

        $twoFactorService = app(\App\Services\TwoFactorAuthenticationService::class);

        return $twoFactorService->verify(
            decrypt($this->two_factor_secret), $code
        );
    }

    /**
     * Validate a two-factor authentication recovery code.
     *
     * @param string $code
     * @return bool
     */
    public function validateTwoFactorRecoveryCode(string $code): bool
    {
        if (! $this->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = $this->two_factor_recovery_codes;

        if (($key = array_search($code, $recoveryCodes)) !== false) {
            unset($recoveryCodes[$key]);

            $this->two_factor_recovery_codes = $recoveryCodes;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'password_confirmation',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has the given role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Check if the user has any of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Check if the user has all of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() === count($roles);
    }

    /**
     * Check if the user has the given permission through any of their roles.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('slug', $permission);
        })->exists();
    }

    /**
     * Check if the user has any of the given permissions through any of their roles.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permissions) {
            $query->whereIn('slug', $permissions);
        })->exists();
    }

    /**
     * Check if the user has all of the given permissions through any of their roles.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }
}
