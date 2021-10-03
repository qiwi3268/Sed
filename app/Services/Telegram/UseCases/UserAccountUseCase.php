<?php

declare(strict_types=1);

namespace App\Services\Telegram\UseCases;

use App\Services\Telegram\Exceptions\TelegramNoticeException;
use App\Models\User;
use App\Lib\Singles\Fio;


final class UserAccountUseCase
{

    /**
     * @throws TelegramNoticeException
     */
    public function createAccount(string $fio, string $tgUserId): void
    {
        $tokenized = Fio::tokenizeString($fio);

        if ($tokenized === false) {
            throw new TelegramNoticeException("Не удалось разобрать ФИО: '$fio'. Убедитесь, что Вы ввели ФИО правильно");
        }

        $fio = new Fio(...$tokenized);

        $user = User::firstByFio($fio);

        if (is_null($user)) {
            throw new TelegramNoticeException("Пользователь с ФИО: '$fio' не найден в базе данных");
        }

        if ($user->telegramAccount()->exists()) {
            throw new TelegramNoticeException("Пользователь $fio уже имеет telegram аккаунт в базе данных");
        }

        $user->telegramAccount()->create([
            'tg_user_id' => $tgUserId
        ]);
    }
}
