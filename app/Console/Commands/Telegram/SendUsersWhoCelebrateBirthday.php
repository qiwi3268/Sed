<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use App\Services\Telegram\UseCases\UsersWhoCelebrateBirthdayUseCase;


final class SendUsersWhoCelebrateBirthday extends Command
{

    protected $signature = 'telegram:send_users_who_celebrate_birthday';

    protected $description = 'Отправляет список пользователей, которые празднуют день рождения (сегодня : ближайший рабочий день]';


    public function handle(UsersWhoCelebrateBirthdayUseCase $useCase): int
    {
        $useCase->sendMessage()
            ? $this->info('Список пользователей успешно отправлен')
            : $this->info('Пользователи отсутствуют');

        return 0;
    }
}
