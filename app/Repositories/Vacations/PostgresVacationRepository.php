<?php

declare(strict_types=1);

namespace App\Repositories\Vacations;

use Illuminate\Database\PostgresConnection;
use App\Repositories\Utils\Builder;
use App\Repositories\Utils\RepositorySupporter;
use App\Lib\DateShifter\DateCalculator;
use DateTimeInterface;


final class PostgresVacationRepository implements VacationRepository
{
    public function __construct(
        private PostgresConnection $connection,
        private DateCalculator $dateCalculator,
        private RepositorySupporter $supporter
    ) {}


    public function getForNext30Days(): array
    {
        $items = $this->connection->select("
            SELECT
                vacations.id,
                vacations.start_at,
                vacations.finish_at,
                vacations.duration,

                users.last_name AS user_last_name,
                users.first_name AS user_first_name,
                users.middle_name AS user_middle_name,

                replacement_users.last_name AS replacement_last_name,
                replacement_users.first_name AS replacement_first_name,
                replacement_users.middle_name AS replacement_middle_name

            FROM vacations

            JOIN users
                ON vacations.user_id = users.id
            LEFT JOIN vacation_replacements
                ON vacations.id = vacation_replacements.vacation_id
            LEFT JOIN users AS replacement_users
                ON vacation_replacements.replacement_user_id = replacement_users.id

            WHERE
                -- Начало в промежутке 30 дней
                vacations.start_at BETWEEN CURRENT_DATE AND CURRENT_DATE + make_interval(days => 30)
                OR
                -- Конец в промежутке 30 дней
                vacations.finish_at BETWEEN CURRENT_DATE AND CURRENT_DATE + make_interval(days => 30)

            ORDER BY vacations.start_at, users.last_name
        ");

        return Builder::create($items)
            ->deepenByPrefix('user_', 'user')
            ->compileJoin('id', 'replacement_', 'replacements')
            ->mutateItems(function (array &$i): void {
                $i['going_to_work_at'] = $this->dateCalculator->getNextWorkingDate(resolve_date($i['finish_at']))->format('Y-m-d');
            })
            ->stripPrefix('user_')
            ->stripPrefix('replacement_')
            ->toArray();
    }

    public function getByYearAndMonth(int $year, int $month): array
    {
        $items = $this->connection->select("
            SELECT
                vacations.id,
                vacations.start_at,
                vacations.finish_at,
                vacations.duration,

                users.last_name AS user_last_name,
                users.first_name AS user_first_name,
                users.middle_name AS user_middle_name,

                replacement_users.last_name AS replacement_last_name,
                replacement_users.first_name AS replacement_first_name,
                replacement_users.middle_name AS replacement_middle_name

            FROM vacations

            JOIN users
                ON vacations.user_id = users.id
            LEFT JOIN vacation_replacements
                ON vacations.id = vacation_replacements.vacation_id
            LEFT JOIN users AS replacement_users
                ON vacation_replacements.replacement_user_id = replacement_users.id

            WHERE
                -- Начало в указанном месяце
                make_date(?, ?, 1) = date_trunc('month', vacations.start_at)
                OR
                -- Конец в указанном месяце
                make_date(?, ?, 1) = date_trunc('month', vacations.finish_at)

            ORDER BY vacations.start_at, users.last_name

        ", [$year, $month, $year, $month]);


        return Builder::create($items)
            ->deepenByPrefix('user_', 'user')
            ->compileJoin('id', 'replacement_', 'replacements')
            ->mutateItems(function (array &$i): void {
                $i['going_to_work_at'] = $this->dateCalculator->getNextWorkingDate(resolve_date($i['finish_at']))->format('Y-m-d');
            })
            ->stripPrefix('user_')
            ->stripPrefix('replacement_')
            ->toArray();
    }


    public function getNext(): array
    {
        $items = $this->connection->select("
            SELECT
                vacations.id,
                vacations.start_at,
                vacations.duration,

                users.id AS user_id,
                users.last_name AS user_last_name,
                users.first_name AS user_first_name,
                users.middle_name AS user_middle_name,

                replacement_users.id AS replacement_id,
                replacement_users.last_name AS replacement_last_name,
                replacement_users.first_name AS replacement_first_name,
                replacement_users.middle_name AS replacement_middle_name

            FROM vacations

            JOIN users
                ON vacations.user_id = users.id
            LEFT JOIN vacation_replacements
                ON vacations.id = vacation_replacements.vacation_id
            LEFT JOIN users AS replacement_users
                ON vacation_replacements.replacement_user_id = replacement_users.id

            WHERE
                -- Начало в будущем времени
                vacations.start_at >= CURRENT_DATE
                OR
                -- Конец в будущем времени
                vacations.finish_at >= CURRENT_DATE

            ORDER BY vacations.start_at, users.last_name
        ");

        return Builder::create($items)
            ->deepenByPrefix('user_', 'user')
            ->compileJoin('id', 'replacement_', 'replacements')
            ->stripPrefix('user_')
            ->stripPrefix('replacement_')
            ->toArray();
    }


    public function getPast(): array
    {
        $items = $this->connection->select("
            SELECT
                vacations.id,
                vacations.start_at,
                vacations.duration,

                users.id AS user_id,
                users.last_name AS user_last_name,
                users.first_name AS user_first_name,
                users.middle_name AS user_middle_name,

                replacement_users.id AS replacement_id,
                replacement_users.last_name AS replacement_last_name,
                replacement_users.first_name AS replacement_first_name,
                replacement_users.middle_name AS replacement_middle_name

            FROM vacations

            JOIN users
                ON vacations.user_id = users.id
            LEFT JOIN vacation_replacements
                ON vacations.id = vacation_replacements.vacation_id
            LEFT JOIN users AS replacement_users
                ON vacation_replacements.replacement_user_id = replacement_users.id

            WHERE
                -- Начало в прошедшем времени
                vacations.start_at < CURRENT_DATE
                AND
                -- Конец в прошедшем времени
                vacations.finish_at < CURRENT_DATE

            ORDER BY vacations.start_at, users.last_name
        ");

        return Builder::create($items)
            ->deepenByPrefix('user_', 'user')
            ->compileJoin('id', 'replacement_', 'replacements')
            ->stripPrefix('user_')
            ->stripPrefix('replacement_')
            ->toArray();
    }


    /**
     * @return int[]
     */
    public function getUsersOnVacationIdsByDate(DateTimeInterface $date): array
    {
        $ids = $this->connection->select("
            SELECT DISTINCT users.id

            FROM users

            JOIN vacations
                ON users.id = vacations.user_id

            WHERE
                ? >= vacations.start_at
                AND
                ? <= vacations.finish_at
        ", [$date->format('Y-m-d'), $date->format('Y-m-d')]);

        return Builder::create($ids)->pluck('id');
    }


    public function getUsersOnVacationCountByDate(DateTimeInterface $date): int
    {
        $result = $this->connection->selectOne("
            SELECT count(*)
            FROM (
                SELECT DISTINCT users.id

                FROM users

                JOIN vacations
                    ON users.id = vacations.user_id

                WHERE
                    ? >= vacations.start_at
                    AND
                    ? <= vacations.finish_at
            ) as tmp
        ", [$date->format('Y-m-d'), $date->format('Y-m-d')]);

        return $result->count;
    }


    public function getUsersOnVacationByDate(DateTimeInterface $date): array
    {
        return $this->connection->select("
            SELECT DISTINCT

                users.last_name,
                users.first_name,
                users.middle_name

            FROM users

            JOIN vacations
                ON users.id = vacations.user_id

            WHERE
                ? >= vacations.start_at
                AND
                ? <= vacations.finish_at

            ORDER BY users.last_name
        ", [$date->format('Y-m-d'), $date->format('Y-m-d')]);
    }


    /**
     * @param DateTimeInterface[] $dates
     */
    public function getUsersOnVacationByStartDates(array $dates): array
    {
        if (empty($dates)) {
            return [];
        }

        $dates = array_map(fn (DateTimeInterface $d) => $d->format('Y-m-d'), $dates);

        return $this->connection->select("
            SELECT
                vacations.start_at,
                vacations.duration,

                users.last_name,
                users.first_name,
                users.middle_name

            FROM vacations

            JOIN users ON vacations.user_id = users.id

            WHERE vacations.start_at IN ({$this->supporter->createPlaceholders($dates)})

            ORDER BY vacations.start_at, users.last_name
        ", $dates);
    }
}
