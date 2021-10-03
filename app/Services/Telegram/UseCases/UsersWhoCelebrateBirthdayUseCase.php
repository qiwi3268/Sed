<?php

declare(strict_types=1);

namespace App\Services\Telegram\UseCases;

use App\Lib\Singles\Fio;
use App\Lib\Singles\Randomizer;
use App\Lib\DateShifter\DateCalculator;
use App\Lib\RouteSynchronization\RouteSynchronizer;
use App\Services\Telegram\CompanyTelegram;
use App\Repositories\Birthdays\BirthdayRepository;
use stdClass;


final class UsersWhoCelebrateBirthdayUseCase
{
    public function __construct(
        private CompanyTelegram $companyTelegram,
        private BirthdayRepository $birthdayRepository,
        private DateCalculator $dateCalculator,
        private RouteSynchronizer $routeSynchronizer
    ) {}


    public function sendMessage(): bool
    {
        if (empty($usersGroupedByDate = $this->getUsersGroupedByDate())) {
            return false;
        }

        $emojis = new Randomizer(['🎉', '🎊', '🎈', '🎁', '🥳', '🤩', '👑', '⭐️', '🎂', '🍾', '🍫', '🍰', '🥧', '🍧', '🍪', '🍹', '🧁']);

        $message = collect($usersGroupedByDate)
            ->map(function (array $users, string $date) use ($emojis): string {
                return implode(
                    PHP_EOL,
                    [
                        "*$date* празднует свой день рождения:",
                        ...array_map(fn (string $fio) => "{$emojis->get()}  $fio", $users)
                    ]
                );
            })
            ->implode(PHP_EOL);

        $url = $this->routeSynchronizer->generateAbsoluteUrl('birthdays');

        $this->companyTelegram->sendMessageToMainChat(
            <<<MD
$message

[Список всех дней рождений]($url)
MD,
            ['parse_mode' => 'markdown']
        );

        return true;
    }


    /**
     * @return array
     * <pre>
     * [
     *     '16.02.2021' => [
     *         'Макаров В.А.',
     *         ...
     *     ],
     *     ...
     * ]
     * </pre>
     */
    private function getUsersGroupedByDate(): array
    {
        $users = $this->birthdayRepository->getByDatesOfBirth(
            $this->dateCalculator->getDatesBeforeNextWorkingDate(now())
        );

        $result = [];

        foreach ($users as $user) {

            $date = resolve_date($user->date_of_birth)->format('d.m');

            $result[$date][] = (new Fio($user->last_name, $user->first_name, $user->middle_name))->getShortFio();
        }
        return $result;
    }
}
