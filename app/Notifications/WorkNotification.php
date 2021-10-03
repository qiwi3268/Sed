<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


final class WorkNotification extends Notification implements LoggedNotification, ShouldQueue
{
    use Queueable;


    public function __construct(
        private string $login,
        private string $password
    ) {}


    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }


    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->success()
            ->subject('Данные для входа в СЭД')
            ->greeting('Здравствуйте!')
            ->line('Направляем Вам логин и пароль для входа в новый модуль подписания СЭД Экспертиза:')
            ->line($this->login)
            ->line($this->password)
            ->action('Временная ссылка для входа в систему', 'http://192.168.1.248/');
    }
}
