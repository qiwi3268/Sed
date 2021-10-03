<?php

declare(strict_types=1);

namespace App\Listeners\SignatureSessions;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Models\User;
use App\Events\SignatureSessions\SignatureSessionCreated;
use App\Lib\RouteSynchronization\RouteSynchronizer;
use App\Services\Telegram\CompanyTelegram;


final class SendSignatureSessionCreatedTelegramMessageToMainChat implements ShouldQueue
{
    use Queueable;


    public function __construct(
        private RouteSynchronizer $routeSynchronizer,
        private CompanyTelegram $companyTelegram
    ) {}


    public function handle(SignatureSessionCreated $event): void
    {
        $url = $this->routeSynchronizer->generateAbsoluteUrl(
            'signatureSession.view',
            ['uuid' => $event->signatureSession->uuid]
        );

        $signers = $event->signers
            ->map(fn (User $u): string => "●  {$u->fio->getShortFio()}")
            ->sort()
            ->implode(PHP_EOL);

        $this->companyTelegram->sendMessageToMainChat(
            <<<MD
📌 Создана [сессия подписания]($url) "{$event->signatureSession->title}"

*Подписанты:*
$signers
MD,
            ['parse_mode' => 'markdown']
        );
    }
}
