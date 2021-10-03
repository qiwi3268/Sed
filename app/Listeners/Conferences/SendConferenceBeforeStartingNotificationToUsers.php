<?php

declare(strict_types=1);

namespace App\Listeners\Conferences;

use Illuminate\Support\Facades\Notification;
use App\Events\Conferences\ConferenceBeforeStarting;
use App\Notifications\Conferences\ConferenceBeforeStartingNotification;


final class SendConferenceBeforeStartingNotificationToUsers
{

    public function handle(ConferenceBeforeStarting $event): void
    {
        Notification::send($event->users, new ConferenceBeforeStartingNotification($event->conference));
    }
}
