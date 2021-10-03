<?php

declare(strict_types=1);

namespace App\Repositories\Conferences;

use App\Repositories\Exceptions\EmptyResponseRepositoryException;

use Illuminate\Database\PostgresConnection;
use App\Repositories\Utils\Builder;


final class PostgresConferenceRepository implements ConferenceRepository
{
    public function __construct(private PostgresConnection $connection)
    {}


    /**
     * @throws EmptyResponseRepositoryException
     */
    public function get(string $conferenceUuid): array
    {
        $conferences = $this->connection->select("
            SELECT
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

            WHERE conferences.uuid = ?

            ORDER BY member_last_name
        ", [$conferenceUuid]);

        $conferences = Builder::create($conferences);

        if ($conferences->isEmpty()) {
            throw new EmptyResponseRepositoryException('conferences', "uuid = $conferenceUuid");
        }

        return $conferences
            ->compileJoin('uuid', 'member_', 'members')
            ->stripPrefix('member_')
            ->singleToArray();
    }

    /**
     * @throws EmptyResponseRepositoryException
     */
    public function getForUpdate(string $conferenceUuid): array
    {
        $conferences = $this->connection->select("
            SELECT
                conferences.uuid,
                conferences.topic,
                conferences.start_at,
                misc_conference_forms.id AS conference_form_id,
                misc_conference_forms.name AS conference_form_label,
                conferences.vks_href,

                vks_connection_responsible_users.id AS vks_connection_responsible_id,
                vks_connection_responsible_users.last_name AS vks_connection_responsible_last_name,
                vks_connection_responsible_users.first_name AS vks_connection_responsible_first_name,
                vks_connection_responsible_users.middle_name AS vks_connection_responsible_middle_name,

                misc_conference_locations.id AS conference_location_id,
                misc_conference_locations.name AS conference_location_label,
                conferences.outer_members,
                conferences.comment,
                conferences.created_at,

                member_users.id AS member_id,
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

            WHERE conferences.uuid = ?

            ORDER BY member_last_name
        ", [$conferenceUuid]);

        $conferences = Builder::create($conferences);

        if ($conferences->isEmpty()) {
            throw new EmptyResponseRepositoryException('conferences', "uuid = $conferenceUuid");
        }

        return $conferences
            ->compileJoin('uuid', 'member_', 'members')
            ->deepenByPrefix('conference_form_', 'conference_form')
            ->deepenByPrefix('conference_location_', 'conference_location')
            ->deepenByPrefix('vks_connection_responsible_', 'vks_connection_responsible')
            ->stripPrefix('member_')
            ->stripPrefix('conference_form_')
            ->stripPrefix('conference_location_')
            ->stripPrefix('vks_connection_responsible_')
            ->singleToArray();
    }
}
