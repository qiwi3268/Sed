<?php

declare(strict_types=1);

namespace App\Repositories\Conferences\Navigation;

use Illuminate\Database\PostgresConnection;
use App\Repositories\Utils\RepositorySupporter;
use App\Repositories\Utils\Builder;
use DateTimeInterface;


final class PostgresConferenceNavigationRepository implements ConferenceNavigationRepository
{
    public function __construct(
        private PostgresConnection $connection,
        private RepositorySupporter $supporter
    ) {}


    public function getTodaysCount(int $userId): int
    {
        $result = $this->connection->selectOne("
            SELECT count(*)
            FROM (
                SELECT DISTINCT conferences.id

                FROM conferences

                JOIN conference_user
                    ON conferences.id = conference_user.conference_id

                WHERE
                    -- Пользователь ответственный за подключение к ВКС
                    -- или в списке участников совещания
                    (
                        conferences.vks_connection_responsible_id = ?
                        OR
                        conference_user.user_id = ?
                    )
                    AND
                    conferences.start_at::date = CURRENT_DATE
            ) as tmp
        ", [$userId, $userId]);

        return $result->count;
    }


    public function getTodays(int $userId): array
    {
        $items = $this->connection->select("
            SELECT DISTINCT
                conferences.id,
                conferences.uuid,
                conferences.topic,
                conferences.start_at,
                misc_conference_forms.name AS conference_form,
                conferences.vks_href,

                users.last_name AS vks_connection_responsible_last_name,
                users.first_name AS vks_connection_responsible_first_name,
                users.middle_name AS vks_connection_responsible_middle_name,

                misc_conference_locations.name AS conference_location,
                conferences.outer_members,
                conferences.comment,
                conferences.created_at

            FROM conferences

            JOIN misc_conference_forms
                ON conferences.misc_conference_form_id = misc_conference_forms.id
            LEFT JOIN users
                ON conferences.vks_connection_responsible_id = users.id
            JOIN misc_conference_locations
                ON conferences.misc_conference_location_id = misc_conference_locations.id
            JOIN conference_user
                ON conferences.id = conference_user.conference_id

            WHERE
                -- Пользователь ответственный за подключение к ВКС
                -- или в списке участников совещания
                (
                    conferences.vks_connection_responsible_id = ?
                    OR
                    conference_user.user_id = ?
                )
                AND
                conferences.start_at::date = CURRENT_DATE

            ORDER BY start_at
        ", [$userId, $userId]);

        $items = Builder::create($items);

        if ($items->isEmpty()) {
            return [];
        }

        $ids = $items->pluck('id');

        $members = $this->connection->select("
            SELECT
                conference_user.conference_id,
                users.last_name,
                users.first_name,
                users.middle_name

            FROM conference_user

            JOIN users
                ON conference_user.user_id = users.id

            WHERE conference_user.conference_id IN ({$this->supporter->createPlaceholders($ids)})

            ORDER BY users.last_name
        ", $ids);

        return $items->insertOuterItems(
            'id',
            'members',
            $members,
            'conference_id'
        )->toArray();
    }


    public function getPlannedCount(int $userId): int
    {
        $result = $this->connection->selectOne("
            SELECT count(*)
            FROM (
                SELECT DISTINCT conferences.id

                FROM conferences

                JOIN conference_user
                    ON conferences.id = conference_user.conference_id

                WHERE
                    -- Пользователь ответственный за подключение к ВКС
                    -- или в списке участников совещания
                    (
                        conferences.vks_connection_responsible_id = ?
                        OR
                        conference_user.user_id = ?
                    )
                    AND
                    conferences.start_at > now()
            ) as tmp
        ", [$userId, $userId]);

        return $result->count;
    }


    public function getPlanned(int $userId): array
    {
        $items = $this->connection->select("
            SELECT DISTINCT
                conferences.id,
                conferences.uuid,
                conferences.topic,
                conferences.start_at,
                misc_conference_forms.name AS conference_form,
                conferences.vks_href,

                users.last_name AS vks_connection_responsible_last_name,
                users.first_name AS vks_connection_responsible_first_name,
                users.middle_name AS vks_connection_responsible_middle_name,

                misc_conference_locations.name AS conference_location,
                conferences.outer_members,
                conferences.comment,
                conferences.created_at

            FROM conferences

            JOIN misc_conference_forms
                ON conferences.misc_conference_form_id = misc_conference_forms.id
            LEFT JOIN users
                ON conferences.vks_connection_responsible_id = users.id
            JOIN misc_conference_locations
                ON conferences.misc_conference_location_id = misc_conference_locations.id
            JOIN conference_user
                ON conferences.id = conference_user.conference_id

            WHERE
                -- Пользователь ответственный за подключение к ВКС
                -- или в списке участников совещания
                (
                    conferences.vks_connection_responsible_id = ?
                    OR
                    conference_user.user_id = ?
                )
                AND
                conferences.start_at > now()

            ORDER BY start_at
        ", [$userId, $userId]);

        $items = Builder::create($items);

        if ($items->isEmpty()) {
            return [];
        }

        $ids = $items->pluck('id');

        $members = $this->connection->select("
            SELECT
                conference_user.conference_id,
                users.last_name,
                users.first_name,
                users.middle_name

            FROM conference_user

            JOIN users
                ON conference_user.user_id = users.id

            WHERE conference_user.conference_id IN ({$this->supporter->createPlaceholders($ids)})

            ORDER BY users.last_name
        ", $ids);

        return $items->insertOuterItems(
            'id',
            'members',
            $members,
            'conference_id'
        )->toArray();
    }


    /**
     * @return string[]
     */
    public function getDatesWithConferencesByYear(int $year): array
    {
        $items = $this->connection->select("
            SELECT DISTINCT start_at::date AS date
            FROM conferences
            WHERE date_part('year', start_at) = ?
            ORDER BY date
        ", [$year]);

        return array_column($items, 'date');
    }


    public function getByDate(DateTimeInterface $date): array
    {
        $items = $this->connection->select("
            SELECT
                conferences.id,
                conferences.uuid,
                conferences.topic,
                conferences.start_at,
                misc_conference_forms.name AS conference_form,
                conferences.vks_href,

                vks_connection_responsible_users.last_name AS vks_connection_responsible_last_name,
                vks_connection_responsible_users.first_name AS vks_connection_responsible_first_name,
                vks_connection_responsible_users.middle_name AS vks_connection_responsible_middle_name,

                misc_conference_locations.name AS conference_location,
                conferences.outer_members,
                conferences.comment,
                conferences.created_at,

                member_users.last_name AS member_last_name,
                member_users.first_name AS member_first_name,
                member_users.middle_name AS member_middle_name

            FROM conferences

            JOIN misc_conference_forms
                ON conferences.misc_conference_form_id = misc_conference_forms.id
            LEFT JOIN users AS vks_connection_responsible_users
                ON conferences.vks_connection_responsible_id = vks_connection_responsible_users.id
            JOIN misc_conference_locations
                ON conferences.misc_conference_location_id = misc_conference_locations.id
            JOIN conference_user
                ON conferences.id = conference_user.conference_id
            JOIN users as member_users
                ON conference_user.user_id = member_users.id

            WHERE conferences.start_at::date = ?

            ORDER BY start_at, member_last_name
        ", [$date->format('Y-m-d')]);

        return Builder::create($items)
            ->compileJoin('id', 'member_', 'members')
            ->stripPrefix('member_')
            ->toArray();
    }
}
