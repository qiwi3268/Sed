<?php

declare(strict_types=1);

namespace App\Services\Telegram\Handlers\Commands;

use App\Services\Telegram\Exceptions\TelegramNoticeException;

use App\Services\Telegram\Handlers\Interfaces\TelegramCommandHandler;
use App\Services\Telegram\UseCases\UserAccountUseCase;


final class CreateAccountCommandHandler extends TelegramCommandHandler
{
    protected static $aliases = ['/create_account'];
    protected static $description = 'Отправьте "/create_account Фамилия Имя Отчество" для создания Вашего telegram аккаунта на сервере"';


    /**
     * @throws TelegramNoticeException
     */
    protected function doHandle(): void
    {
        $argument = $this->getCommandArgument();

        if (is_null($argument)) {
            throw new TelegramNoticeException('При вызове команды необходимо ввести фамилию, имя и отчество');
        }

        app(UserAccountUseCase::class)->createAccount(
            $argument,
            (string) $this->update->user()->id
        );

        $this->sendSuccessMessageToTargetChat("В базу данных добавлен telegram аккаунт пользователя");
    }
}
