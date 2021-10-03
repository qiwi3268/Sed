<?php

declare(strict_types=1);

namespace App\Listeners\Conferences;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Events\Conferences\ConferenceCreated;
use App\Services\Telegram\CompanyTelegram;
use App\Services\Conferences\TelegramMessageFactory;


final class SendConferenceCreatedTelegramMessageToMainChat implements ShouldQueue
{
    use Queueable;


    public function __construct(private CompanyTelegram $companyTelegram)
    {}


    public function handle(ConferenceCreated $event): void
    {
        $this->companyTelegram->sendMessageToMainChat(
            TelegramMessageFactory::getCreatedText($event->conference),
            [
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true
            ]
        );
    }
}
