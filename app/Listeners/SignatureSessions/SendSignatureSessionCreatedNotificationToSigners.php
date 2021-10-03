<?php

declare(strict_types=1);

namespace App\Listeners\SignatureSessions;

use Illuminate\Support\Facades\Notification;
use App\Lib\RouteSynchronization\RouteSynchronizer;
use App\Events\SignatureSessions\SignatureSessionCreated;
use App\Notifications\SignatureSessions\SignatureSessionCreatedNotification;


final class SendSignatureSessionCreatedNotificationToSigners
{

    public function __construct(private RouteSynchronizer $routeSynchronizer)
    {}


    public function handle(SignatureSessionCreated $event): void
    {
        $url = $this->routeSynchronizer->generateAbsoluteUrl(
            'signatureSession.signing',
            ['uuid' => $event->signatureSession->uuid]
        );

        Notification::send($event->signers, new SignatureSessionCreatedNotification($url));
    }
}
