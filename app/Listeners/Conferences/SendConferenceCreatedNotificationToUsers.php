<?php

declare(strict_types=1);

namespace App\Listeners\Conferences;

use Illuminate\Support\Facades\Notification;
use App\Events\Conferences\ConferenceCreated;
use App\Notifications\Conferences\ConferenceCreatedNotification;


final class SendConferenceCreatedNotificationToUsers
{

    public function handle(ConferenceCreated $event): void
    {
        Notification::send($event->users, new ConferenceCreatedNotification($event->conference));
    }
}
