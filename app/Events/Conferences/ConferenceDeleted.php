<?php

declare(strict_types=1);

namespace App\Events\Conferences;

use App\Events\AppEvent;
use DateTimeImmutable;


final class ConferenceDeleted extends AppEvent
{

    /**
     * Конструктор не принимает модель Conference, т.к. событие вызывается после того,
     * как строка совещания будет удалена из БД
     */
    public function __construct(public string $topic, public DateTimeImmutable $startAt)
    {}
}
