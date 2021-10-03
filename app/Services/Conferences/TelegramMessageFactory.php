<?php

declare(strict_types=1);

namespace App\Services\Conferences;

use App\Models\Conferences\Conference;
use App\Models\User;
use App\Lib\Singles\Inflector;
use App\Lib\RouteSynchronization\RouteSynchronizer;
use App\Jobs\Conferences\ProcessConferencesBeforeStarting;


final class TelegramMessageFactory
{

    public static function getCreatedText(Conference $conference): string
    {
        return self::getText($conference, '📌 Запланировано совещание');
    }


    public static function getUpdatedText(Conference $conference): string
    {
        return self::getText($conference, '❗ Совещание изменено');
    }


    public static function getBeforeStartingText(Conference $conference): string
    {
        $inflector = new Inflector('минута', 'минуты', 'минут');

        return self::getText(
            $conference,
            // Фактическое время перед началом не вычисляется, т.к. предполагается,
            // что ProcessConferencesBeforeStarting запускается каждую минуту
            "⏰ До начала совещания {$inflector->inflect(ProcessConferencesBeforeStarting::MINUTES_BEFORE_START)}"
        );
    }


    private static function getText(Conference $conference, string $subject): string
    {
        $url = app(RouteSynchronizer::class)->generateAbsoluteUrl(
            'conferences.navigation.items.my.planned'
        );

        $vksHref = is_null($conference->vks_href)
            ? ''
            : "[Ссылка на подключение]($conference->vks_href)";

        $vksConnectionResponsible = is_null($conference->vks_connection_responsible_id)
            ? ''
            : "*Ответственный за подключение:* {$conference->vksConnectionResponsible->fio->getShortFio()}";

        $members = $conference->members
            ->sortCompanyUsers()
            ->map(fn(User $u): string => "●  {$u->fio->getShortFio()}")
            ->implode(PHP_EOL);

        $outerMembers = is_null($conference->outer_members)
            ? ''
            : "*Внешние участники:* $conference->outer_members";

        $comment = is_null($conference->comment)
            ? ''
            : "*Комментарий:* $conference->comment";

        $text = <<<MD
$subject

*Тема:* $conference->topic

*Дата начала:* {$conference->start_at->format('d.m.Y H:i')}
*Форма:* {$conference->miscConferenceFormName->name}
*Место проведения:* {$conference->miscConferenceLocationName->name}

$vksHref
$vksConnectionResponsible

*Участники:*
$members

$outerMembers
$comment

[Список всех совещаний]($url)
MD;

        return str_remove_duplicate_nl($text);
    }
}
