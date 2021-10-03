<?php

declare(strict_types=1);

namespace App\Lib\Permissions;

use LogicException;
use App\Lib\Singles\HashArray;


final class Permissions
{
    /**
     * Список разрешений. Ключ и значение должны совпадать
     */
    public const LIST = [
        'manage_vacations'   => 'manage_vacations',
        'manage_conferences' => 'manage_conferences'
    ];

    private HashArray $permissions;


    /**
     * @throws LogicException
     */
    public function __construct(array $permissions)
    {
        $this->permissions = new HashArray();

        foreach ($permissions as $permission) {

            $this->ensurePermission($permission);

            if ($this->permissions->has($permission)) {
                throw new LogicException("Разрешение: '$permission' уже добавлено");
            } else {
                $this->permissions->add($permission);
            }
        }
    }


    public function has(string $permission): bool
    {
        $this->ensurePermission($permission);
        return $this->permissions->has($permission);
    }


    /**
     * @throws LogicException
     */
    private function ensurePermission(string $permission): void
    {
        if (!array_key_exists($permission, self::LIST)) {
            throw new LogicException("Разрешение: '$permission' отсутствует в списке");
        }
    }
}
