<?php

declare(strict_types=1);

namespace App\Services\Telegram\Handlers\Updates;

use App\Models\Telegram\Polls\TelegramPollAtWork;
use App\Services\Telegram\Handlers\Interfaces\TelegramUpdateHandler;
use App\Services\Telegram\UseCases\AtWorkPollUseCase;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;


final class AtWorkPollUpdateHandler extends TelegramUpdateHandler
{
    /**
     * Обработка ответа на опрос, который существует в БД
     */
    public static function trigger(Update $update, TeleBot $bot): bool
    {
        return isset($update->poll_answer)
            && TelegramPollAtWork::whereTgPollId($update->poll_answer->poll_id)->exists();
    }


    protected function doHandle(): void
    {
        app(AtWorkPollUseCase::class)->handlePollAnswer(
            $this->update->poll_answer->poll_id,
            (string) $this->update->user()->id,
            $this->update->poll_answer->option_ids,
        );
    }
}
