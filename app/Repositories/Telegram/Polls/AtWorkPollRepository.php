<?php

declare(strict_types=1);

namespace App\Repositories\Telegram\Polls;

use App\Repositories\Exceptions\EmptyResponseRepositoryException;
use App\Repositories\Exceptions\IneffectiveQueryRepositoryException;

use DateTimeInterface;


interface AtWorkPollRepository
{
    public function getActiveOptions(): array;

    /**
     * @throws EmptyResponseRepositoryException
     */
    public function getOpenPollByDate(DateTimeInterface $date): object;

    public function getByDate(DateTimeInterface $date): array;

    public function createAnswer(int $telegramAccountId, string $tgPollId, int $optionIndex): void;

    /**
     * @throws IneffectiveQueryRepositoryException
     */
    public function deleteAnswers(int $telegramAccountId, string $tgPollId): void;

    /**
     * @throws IneffectiveQueryRepositoryException
     */
    public function stopPoll(int $telegramPollAtWorkId): void;
}
