<?php

declare(strict_types=1);

namespace App\Listeners\Conferences;

use Illuminate\Support\Facades\Notification;
use App\Events\Conferences\ConferenceUpdated;
use App\Notifications\Conferences\ConferenceUpdatedNotification;


final class SendConferenceUpdatedNotificationToUsers
{
    public function handle(ConferenceUpdated $event): void
    {
        Notification::send($event->users, new ConferenceUpdatedNotification($event->conference));
    }
}
