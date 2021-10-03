<?php

declare(strict_types=1);

namespace App\Services\Telegram\UseCases;

use Throwable;
use App\Services\Telegram\Exceptions\TelegramErrorException;

use Illuminate\Support\Facades\DB;
use App\Models\Telegram\TelegramAccount;
use App\Models\Telegram\Polls\TelegramPoll;
use App\Models\Telegram\Polls\TelegramPollAtWork;
use App\Services\Telegram\CompanyTelegram;
use App\Repositories\Telegram\Polls\AtWorkPollRepository;
use Webmozart\Assert\Assert;
use stdClass;


final class AtWorkPollUseCase
{
    public const QUESTION = 'На работе';


    public function __construct(
        private CompanyTelegram $companyTelegram,
        private AtWorkPollRepository $repository
    ) {}


    /**
     * @throws Throwable
     */
    public function startPoll(): void
    {
        Assert::minCount($options = $this->repository->getActiveOptions(), 2);

        $message = $this->companyTelegram->sendPollToMainChat([
            'question'     => self::QUESTION,
            'options'      => array_map(fn (stdClass $o): string => $o->name, $options),
            'is_anonymous' => false
        ]);

        DB::beginTransaction();

        try {

            $telegramPoll = TelegramPoll::create([
                'tg_message_id'   => $message->message_id,
                'tg_from_user_id' => $message->from->id,
                'tg_chat_id'      => $message->chat->id,
                'tg_poll_id'      => $message->poll->id
            ]);

            /** @var TelegramPollAtWork $telegramPollAtWork */
            $telegramPollAtWork = $telegramPoll->pollAtWork()->create();

            $attachData = [];

            foreach ($options as $index => $option) {
                $attachData[$option->id] = ['option_index' => $index];
            }

            $telegramPollAtWork->createdOptions()->attach($attachData);

        } catch (Throwable $e) {
            DB::rollBack();
            $this->companyTelegram->deletePollFromMainChat((string) $message->message_id);
            throw $e;
        }
        DB::commit();
    }


    /**
     * @throws TelegramErrorException
     */
    public function handlePollAnswer(string $tgPollId, string $tgUserId, array $options): void
    {
        $telegramAccountId = TelegramAccount::whereTgUserId($tgUserId)->first(['id'])?->id
            ?? throw new TelegramErrorException("Telegram аккаунт пользователя с id: '$tgUserId' отсутствует в базе данных. Голос не обработан");

        if (empty($options)) {
            $this->repository->deleteAnswers($telegramAccountId, $tgPollId);
        } else {
            $this->repository->createAnswer($telegramAccountId, $tgPollId, array_shift($options));
        }
    }


    public function stopPoll(int $telegramPollAtWorkId, string $tgMessageId): void
    {
        $this->companyTelegram->stopPollInMainChat($tgMessageId);
        $this->repository->stopPoll($telegramPollAtWorkId);
    }
}
