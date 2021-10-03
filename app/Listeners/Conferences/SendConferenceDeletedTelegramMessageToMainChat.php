<?php

declare(strict_types=1);

namespace App\Listeners\Conferences;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Lib\RouteSynchronization\RouteSynchronizer;
use App\Events\Conferences\ConferenceDeleted;
use App\Services\Telegram\CompanyTelegram;


final class SendConferenceDeletedTelegramMessageToMainChat implements ShouldQueue
{
    use Queueable;


    public function __construct(
        private CompanyTelegram $companyTelegram,
        private RouteSynchronizer $routeSynchronizer
    ) {}


    public function handle(ConferenceDeleted $event): void
    {
        $url = $this->routeSynchronizer->generateAbsoluteUrl(
            'conferences.navigation.items.my.planned'
        );

        $this->companyTelegram->sendMessageToMainChat(
            <<<MD
❗ Совещание отменено

*Тема:* $event->topic
*Дата начала:* {$event->startAt->format('d.m.Y H:i')}

[Список всех совещаний]($url)
MD,
            ['parse_mode' => 'markdown']
        );
    }
}
