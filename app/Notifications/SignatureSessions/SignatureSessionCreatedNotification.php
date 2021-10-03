<?php

declare(strict_types=1);

namespace App\Notifications\SignatureSessions;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\LoggedNotification;


final class SignatureSessionCreatedNotification extends Notification implements LoggedNotification, ShouldQueue
{
    use Queueable;


    public function __construct(private string $url)
    {}


    public function via(): array
    {
        return ['mail'];
    }


    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->subject('Подписание документа')
            ->greeting('Здравствуйте!')
            ->line('Вы назначены на подписание документа!')
            ->action('Подписать документ', $this->url);
    }
}
