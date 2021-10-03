<?php

declare(strict_types=1);

namespace App\Notifications\Conferences;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Conferences\Conference;
use App\Notifications\LoggedNotification;
use App\Services\Conferences\MailNotificationFactory;


final class ConferenceUpdatedNotification extends Notification implements LoggedNotification, ShouldQueue
{
    use Queueable;


    /**
     * @param Conference $conference модель со всеми полями
     */
    public function __construct(private Conference $conference)
    {}


    public function via(): array
    {
        return ['mail'];
    }


    public function toMail(): MailMessage
    {
        return MailNotificationFactory::getUpdatedMailMessage($this->conference);
    }
}
