<?php

declare(strict_types=1);

namespace App\Services\Conferences;

use Illuminate\Notifications\Messages\MailMessage;
use App\Lib\Singles\Inflector;
use App\Models\Conferences\Conference;
use App\Jobs\Conferences\ProcessConferencesBeforeStarting;


final class MailNotificationFactory
{

    public static function getCreatedMailMessage(Conference $conference): MailMessage
    {
        return self::getMailMessage(
            $conference,
            'Запланировано совещание',
            'Вы назначены на совещание!'
        );
    }


    public static function getUpdatedMailMessage(Conference $conference): MailMessage
    {
        return self::getMailMessage(
            $conference,
            'Совещание изменено',
            'Вы назначены на совещание!'
        );
    }


    public static function getBeforeStartingMailMessage(Conference $conference): MailMessage
    {
        $inflector = new Inflector('минута', 'минуты', 'минут');

        return self::getMailMessage(
            $conference,
            'Совещание скоро начнётся',
            // Фактическое время перед началом не вычисляется, т.к. предполагается,
            // что ProcessConferencesBeforeStarting запускается каждую минуту
            "Вы назначены на совещание, которое начнётся через {$inflector->inflect(ProcessConferencesBeforeStarting::MINUTES_BEFORE_START)}"
        );
    }


    private static function getMailMessage(Conference $conference, string $subject, string $firstLine): MailMessage
    {
        $mailMessage = (new MailMessage())
            ->subject($subject)
            ->greeting('Здравствуйте!')
            ->line($firstLine)
            ->line("Тема: $conference->topic")
            ->line("Дата начала: {$conference->start_at->format('d.m.Y H:i')}")
            ->line("Форма: {$conference->miscConferenceFormName->name}");


        if (!is_null($conference->vks_href)) {
            $mailMessage->action('Подключиться', $conference->vks_href);
        }

        if (!is_null($conference->vks_connection_responsible_id)) {
            $mailMessage->line("Ответственный за подключение: {$conference->vksConnectionResponsible->fio->getShortFio()}");
        }

        $mailMessage
            ->line("Место проведения: {$conference->miscConferenceLocationName->name}")
            ->line('Участники:');

        foreach ($conference->members->sort() as $member) {
            $mailMessage->line("●  {$member->fio->getShortFio()}");
        }

        if (!is_null($conference->outer_members)) {
            $mailMessage->line("Внешние участники: $conference->outer_members");
        }

        if (!is_null($conference->comment)) {
            $mailMessage->line("Комментарий: $conference->comment");
        }

        return $mailMessage;
    }
}
