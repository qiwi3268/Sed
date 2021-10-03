<?php

declare(strict_types=1);

namespace App\Services\Conferences;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Conferences\Conference;


final class UsersFactory
{

    public static function getUsers(Conference $conference): Collection
    {
        $vksConnectionResponsible = $conference->vksConnectionResponsible;

        // Важно, чтобы была новая коллекция, а не ссылка на отношение, т.к.
        // иначе ответственный за подключение к ВКС добавится в коллекцию отношений
        $users = new Collection($conference->members);

        // Добавление ответственного за подключение к ВКС, если он отсутствует в участниках совещания
        if (!is_null($vksConnectionResponsible) && !$users->contains($vksConnectionResponsible)) {
            $users->add($vksConnectionResponsible);
        }

        return $users->sortCompanyUsers();
    }
}
