<?php

declare(strict_types=1);

namespace App\Services\Telegram\UseCases;

use App\Lib\Singles\Fio;
use App\Lib\Singles\Inflector;
use App\Lib\DateShifter\DateCalculator;
use App\Lib\RouteSynchronization\RouteSynchronizer;
use App\Services\Telegram\CompanyTelegram;
use App\Repositories\Vacations\VacationRepository;


final class UsersWhoGoOnVacationUseCase
{
    public function __construct(
        private CompanyTelegram $companyTelegram,
        private VacationRepository $vacationRepository,
        private DateCalculator $dateCalculator,
        private RouteSynchronizer $routeSynchronizer
    ) {}


    public function sendMessage(): bool
    {
        if (empty($usersGroupedByDate = $this->getUsersGroupedByDate())) {
            return false;
        }

        $inflector = new Inflector('день', 'дня', 'дней');

        $message = collect($usersGroupedByDate)
            ->map(function (array $users, string $date) use ($inflector): string {
                return implode(
                    PHP_EOL,
                    [
                        "*$date* запланирован отпуск:",
                        ...array_map(fn (array $u): string => "●  {$u['fio']} на {$inflector->inflect($u['duration'])}", $users)
                    ]
                );
            })
            ->implode(PHP_EOL);

        $url = $this->routeSynchronizer->generateAbsoluteUrl('vacations');

        $this->companyTelegram->sendMessageToMainChat(
            <<<MD
$message

[Список всех отпусков]($url)
MD,
            ['parse_mode' => 'markdown']
        );

        return true;
    }


    /**
     * @return array
     * <pre>
     * [
     *     '13.08.2021' => [
     *         [
     *             'duration' => 14,
     *             'fio'      => 'Макаров В.А.'
     *         ],
     *         ...
     *     ],
     *     ...
     * ]
     * </pre>
     */
    private function getUsersGroupedByDate(): array
    {
        $users = $this->vacationRepository->getUsersOnVacationByStartDates(
            $this->dateCalculator->getDatesBeforeNextWorkingDate(now())
        );

        $result = [];

        foreach ($users as $user) {

            $date = resolve_date($user->start_at)->format('d.m.Y');

            $result[$date][] = [
                'duration' => $user->duration,
                'fio'      => (new Fio($user->last_name, $user->first_name, $user->middle_name))->getShortFio()
            ];
        }
        return $result;
    }
}
