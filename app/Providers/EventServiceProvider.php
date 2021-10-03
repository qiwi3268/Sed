<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;

use App\Events\SignatureSessions\SignatureSessionCreated;
use App\Events\SignatureSessions\SignatureSessionFinished;
use App\Events\Conferences\ConferenceCreated;
use App\Events\Conferences\ConferenceUpdated;
use App\Events\Conferences\ConferenceDeleted;
use App\Events\Conferences\ConferenceBeforeStarting;
use App\Listeners\LogNotificationEmail;
use App\Listeners\SignatureSessions\SendSignatureSessionCreatedNotificationToSigners;
use App\Listeners\SignatureSessions\SendSignatureSessionCreatedTelegramMessageToMainChat;
use App\Listeners\SignatureSessions\CreateArchiveAndSendSignatureSessionFinishedNotificationToAuthor;
use App\Listeners\Conferences\SendConferenceCreatedNotificationToUsers;
use App\Listeners\Conferences\SendConferenceCreatedTelegramMessageToMainChat;
use App\Listeners\Conferences\SendConferenceUpdatedNotificationToUsers;
use App\Listeners\Conferences\SendConferenceUpdatedTelegramMessageToMainChat;
use App\Listeners\Conferences\SendConferenceDeletedTelegramMessageToMainChat;
use App\Listeners\Conferences\SendConferenceBeforeStartingNotificationToUsers;
use App\Listeners\Conferences\SendConferenceBeforeStartingTelegramMessageToMainChat;


class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        MessageSent::class => [
            LogNotificationEmail::class,
        ],
        SignatureSessionCreated::class => [
            SendSignatureSessionCreatedNotificationToSigners::class,
            SendSignatureSessionCreatedTelegramMessageToMainChat::class
        ],
        SignatureSessionFinished::class => [
            CreateArchiveAndSendSignatureSessionFinishedNotificationToAuthor::class
        ],
        ConferenceCreated::class => [
            SendConferenceCreatedNotificationToUsers::class,
            SendConferenceCreatedTelegramMessageToMainChat::class
        ],
        ConferenceUpdated::class => [
            SendConferenceUpdatedNotificationToUsers::class,
            SendConferenceUpdatedTelegramMessageToMainChat::class
        ],
        ConferenceDeleted::class => [
            SendConferenceDeletedTelegramMessageToMainChat::class
        ],
        ConferenceBeforeStarting::class => [
            SendConferenceBeforeStartingNotificationToUsers::class,
            SendConferenceBeforeStartingTelegramMessageToMainChat::class
        ]
    ];


    public function boot()
    {
        //
    }
}
