<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use App\Lib\Singles\Fio;
use App\Lib\Permissions\Permissions;

use App\Models\Telegram\TelegramAccount;
use App\Models\Traits\Casts\FioCast;


/**
 * @property Fio $fio
 * @property-read TelegramAccount|null $telegramAccount
 * @property-read Collection|Permission[] $permissions
 * @mixin IdeHelperUser
 */
final class User extends AppModel implements
    AuthenticatableContract,
    AuthorizableContract,
    JWTSubject,
    MiscPresentable
{
    use Authenticatable, Authorizable, Notifiable, FioCast;

    protected $casts = [
        'last_activity_at' => 'datetime',
        'date_of_birth'    => 'date',
    ];


    public function getPermissions(): Permissions
    {
        return new Permissions($this->permissions->map->system_value->toArray());
    }


    public function getMiscLabel(): string
    {
        return $this->fio->getLongFio();
    }


    public function getMiscId(): int
    {
        return $this->getRequiredAttribute('id');
    }


    public function updateLastActivity(): void
    {
        $this->last_activity_at = now();
        $this->save();
    }


    public static function firstByFio(Fio $fio): ?self
    {
        return self::where([
            ['last_name',   '=', $fio->getLastName()],
            ['first_name',  '=', $fio->getFirstName()],
            ['middle_name', '=', $fio->getMiddleName()]
        ])->first();
    }


    public function telegramAccount(): HasOne
    {
        return $this->hasOne(TelegramAccount::class);
    }


    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }


    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
