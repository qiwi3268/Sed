<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use App\Notifications\LoggedNotification;
use App\Models\Notifications\NotificationEmail;
use Webmozart\Assert\Assert;


/**
 * Логирование почты, отправленной классами уведомлений
 */
final class LogNotificationEmail
{

    public function handle(MessageSent $event): void
    {
        if (!$this->needReporting($event)) {
            return;
        }

        $subject = $event->message->getSubject();
        $emails = array_keys($event->message->getTo());

        Assert::notEmpty($subject);
        Assert::allEmail($emails);

        foreach ($emails as $email) {

            NotificationEmail::create([
                'subject' => $subject,
                'email'   => $email
            ]);
        }
    }


    /**
     * Отправлено ли письмо из класса уведомления, письма которого требуется логировать
     */
    private function needReporting(MessageSent $event): bool
    {
        // Определение класса уведомления по внутреннему свойству мета информации
        if (isset($event->data['__laravel_notification'])) {

            return is_a(
                $event->data['__laravel_notification'],
                LoggedNotification::class,
                true
            );
        }
        return false;
    }
}
