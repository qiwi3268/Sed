<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use App\Services\Telegram\UseCases\UsersWhoGoOnVacationUseCase;


final class SendUsersWhoGoOnVacation extends Command
{

    protected $signature = 'telegram:send_users_who_go_on_vacation';

    protected $description = 'Отправляет список пользователей, которые уходят в отпуск в период (сегодня : ближайший рабочий день]';


    public function handle(UsersWhoGoOnVacationUseCase $useCase): int
    {
        $useCase->sendMessage()
            ? $this->info('Список пользователей успешно отправлен')
            : $this->info('Пользователи отсутствуют');

        return 0;
    }
}
