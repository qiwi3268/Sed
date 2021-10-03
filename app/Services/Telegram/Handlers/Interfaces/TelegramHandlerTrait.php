<?php

declare(strict_types=1);

namespace App\Services\Telegram\Handlers\Interfaces;

use Exception;
use RuntimeException;
use Illuminate\Database\QueryException;
use App\Repositories\Exceptions\RepositoryException;
use App\Services\Telegram\Exceptions\TelegramNoticeException;
use App\Services\Telegram\Exceptions\TelegramErrorException;

use Illuminate\Support\Facades\Log;
use App\Models\Telegram\Polls\TelegramPoll;
use WeStacks\TeleBot\Interfaces\UpdateHandler;


trait TelegramHandlerTrait
{
    protected string $targetChatId;


    /**
     * Список исключений, которые не требуется логировать
     */
    protected array $dontLog = [
        TelegramNoticeException::class
    ];


    public function handle(): void
    {
        $this->targetChatId = $this->getTargetChatId();

        try {
            $this->doHandle();
        } catch (Exception $e) {

            if (!in_array($e::class, $this->dontLog)) {
                Log::channel('telegram')->error($e, [
                    'telegram_update' => $this->update
                ]);
            }

            if (!$this->handleException($e)) {

                if ($e instanceof TelegramNoticeException) {
                    $this->sendNoticeMessageToTargetChat($e->getMessage());
                } elseif ($e instanceof TelegramErrorException) {
                    $this->sendErrorMessageToTargetChat($e->getMessage());
                } elseif ($e instanceof QueryException || $e instanceof RepositoryException) {
                    $this->sendErrorMessageToTargetChat('Ошибка при работе с базой данных');
                } else {
                    $this->sendErrorMessageToTargetChat('Неизвестная ошибка');
                }
            }
        }
    }


    /**
     * Содержит в себе логику определения чата,
     * в который требуется отправлять ответные сообщения
     *
     * @throws RuntimeException
     */
    protected function getTargetChatId(): string
    {
        /** @var UpdateHandler $this */
        $update = $this->update;

        if ($update->is('message')) {

            // Если пришло сообщение, то ответ всегда в этот же чат
            return (string) $update->message->chat->id;

        } elseif ($update->is('poll_answer')) {

            // Если пришёл ответ на опрос, то id чата отсутствует в update.
            // Поэтому id берётся из БД
            return TelegramPoll::whereTgPollId($update->poll_answer->poll_id)
                ->firstOrFail('tg_chat_id')
                ->tg_chat_id;

        } else {
            throw new RuntimeException("Метод TelegramHandlerTrait::getTargetChatId не обработал тип обновления: '{$update->type()}'");
        }
    }


    abstract protected function doHandle(): void;


    /**
     * Обрабатывает исключение, возникшее в методе doHandle
     *
     * @return bool вернуть false, если обработка исключения не удалась или не производилась
     */
    protected function handleException(Exception $e): bool
    {
        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Отправка сообщений
    |--------------------------------------------------------------------------
    */


    protected function sendErrorMessageToTargetChat(string $text): void
    {
        $markdown = <<<MD
*Ошибка!* 🙀

$text
MD;
        $this->sendMessageToTargetChat($markdown, ['parse_mode' => 'markdown']);
    }


    protected function sendSuccessMessageToTargetChat(string $text): void
    {
        $markdown = <<<MD
*Успех!* 🥳

$text
MD;
        $this->sendMessageToTargetChat($markdown, ['parse_mode' => 'markdown']);
    }


    protected function sendNoticeMessageToTargetChat(string $text): void
    {
        $markdown = <<<MD
*Внимание*❗️

$text
MD;
        $this->sendMessageToTargetChat($markdown, ['parse_mode' => 'markdown']);
    }


    protected function sendMessageToTargetChat(string $text, array $parameters = []): void
    {
        $this->sendMessage(array_merge([
            'chat_id' => $this->targetChatId,
            'text'    => $text
        ], $parameters));
    }
}
