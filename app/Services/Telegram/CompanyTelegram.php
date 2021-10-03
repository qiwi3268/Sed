<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use RuntimeException;
use WeStacks\TeleBot\Exception\TeleBotObjectException;

use WeStacks\TeleBot\Objects\Message;
use WeStacks\TeleBot\Objects\Poll;
use WeStacks\TeleBot\TeleBot;
use WeStacks\TeleBot\BotManager;


final class CompanyTelegram
{
    public const COMPANY_MAIN_BOT = 'company_main_bot';


    public function __construct(
        private BotManager $botManager,
        private string $mainChatId
    ) {}


    /**
     * @throws RuntimeException
     */
    public function getMainBot(): TeleBot
    {
        try {
            return $this->botManager->bot(self::COMPANY_MAIN_BOT);
        } catch (TeleBotObjectException $e) {
            throw new RuntimeException('Ошибка при получении основного бота компании', previous: $e);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Синтаксический сахар
    |--------------------------------------------------------------------------
    */

    public function sendPollToMainChat(array $parameters): Message
    {
        return $this->getMainBot()->sendPoll(array_merge(
            ['chat_id' => $this->mainChatId],
            $parameters
        ));
    }


    public function deletePollFromMainChat(string $tgMessageId): void
    {
        $this->getMainBot()->deleteMessage([
            'chat_id'    => $this->mainChatId,
            'message_id' => $tgMessageId
        ]);
    }


    public function stopPollInMainChat(string $tgMessageId): Poll
    {
        return $this->getMainBot()->stopPoll([
            'chat_id'    => $this->mainChatId,
            'message_id' => $tgMessageId
        ]);
    }


    public function sendMessageToMainChat(string $text, array $parameters = []): Message
    {
        return $this->getMainBot()->sendMessage(array_merge([
            'chat_id' => $this->mainChatId,
            'text'    => $text
        ], $parameters));
    }
}
