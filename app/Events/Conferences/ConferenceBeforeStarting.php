<?php

declare(strict_types=1);

namespace App\Events\Conferences;

use Illuminate\Database\Eloquent\Collection;
use App\Events\AppEvent;
use App\Models\Conferences\Conference;
use App\Services\Conferences\UsersFactory;


final class ConferenceBeforeStarting extends AppEvent
{

    /**
     * Участники совещания, включая ответственного за подключение к ВКС (если есть)
     */
    public Collection $users;


    /**
     * @param Conference $conference модель со всеми полями
     */
    public function __construct(public Conference $conference)
    {
        $this->users = UsersFactory::getUsers($conference);
    }
}
