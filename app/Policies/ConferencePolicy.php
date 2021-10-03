<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Conferences\Conference;
use App\Lib\Permissions\Permissions;


final class ConferencePolicy
{
    use HandlesAuthorization;


    public function update(User $user, Conference $conference): Response
    {
        return $user->getPermissions()->has(Permissions::LIST['manage_conferences']) && $conference->start_at > now()
            ? $this->allow()
            : $this->deny('Пользователь не может редактировать совещание');
    }


    public function delete(User $user, Conference $conference): Response
    {
        return $user->getPermissions()->has(Permissions::LIST['manage_conferences']) && $conference->start_at > now()
            ? $this->allow()
            : $this->deny('Пользователь не может удалить совещание');
    }
}
