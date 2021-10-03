<?php

namespace App\Jobs\Conferences;

use App\Models\Conferences\Conference;
use App\Events\Conferences\ConferenceBeforeStarting;


final class ProcessConferencesBeforeStarting
{
    public const MINUTES_BEFORE_START = 30;


    public function handle(): void
    {
        Conference::whereIsNotificationBeforeStartSent(false)
            ->where('start_at', '<=', now()->addMinutes(self::MINUTES_BEFORE_START))
            ->orderBy('start_at')
            ->get()
            ->each(function (Conference $c): void {
                ConferenceBeforeStarting::dispatch($c->notificationBeforeStartSent());
            });
    }
}
