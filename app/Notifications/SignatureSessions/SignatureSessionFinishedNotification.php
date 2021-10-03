<?php

declare(strict_types=1);

namespace App\Notifications\SignatureSessions;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\LoggedNotification;


final class SignatureSessionFinishedNotification extends Notification implements LoggedNotification
{

    public function __construct(
        private string $signingUrl,
        private string $archiveFilePath,
        private string $archiveFileName
    ) {}


    public function via(): array
    {
        return ['mail'];
    }


    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->success()
            ->subject('Сессия подписания успешно завершена')
            ->greeting('Здравствуйте!')
            ->line('Все назначенные пользователи выполнили подписание!')
            ->action('Просмотр сессии подписания', $this->signingUrl)
            ->attach($this->archiveFilePath, [
                'as' => $this->archiveFileName
            ]);
    }
}
