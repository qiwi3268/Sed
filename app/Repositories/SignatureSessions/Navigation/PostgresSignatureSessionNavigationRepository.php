<?php

declare(strict_types=1);

namespace App\Repositories\SignatureSessions\Navigation;

use Illuminate\Database\PostgresConnection;
use App\Lib\DatabaseMagicNumbers\MagicNumbersContainer;
use App\Repositories\Utils\RepositorySupporter;
use App\Repositories\Utils\Builder;


final class PostgresSignatureSessionNavigationRepository implements SignatureSessionNavigationRepository
{
    public function __construct(
        private PostgresConnection $connection,
        private MagicNumbersContainer $sessionStatuses,
        private MagicNumbersContainer $signerStatuses,
        private RepositorySupporter $supporter
    ) {}


    public function getWaitingActionCount(int $userId): int
    {
        $result = $this->connection->selectOne("
            SELECT count(*)
            FROM signature_session_signer_assignments
            WHERE
                signer_id = ?
                AND
                signature_session_signer_status_id = {$this->signerStatuses->get('Ожидает подписания')}
        ", [$userId]);

        return $result->count;
    }


    public function getWaitingAction(int $userId, int $limit, int $offset): array
    {
        return $this->connection->select("
            SELECT DISTINCT
                signature_sessions.uuid,
                signature_sessions.title,
                signature_sessions.created_at,

                signature_session_statuses.id AS signature_session_status_id,
                signature_session_statuses.name AS signature_session_status_name,

                users.last_name AS author_last_name,
                users.first_name AS author_first_name,
                users.middle_name AS author_middle_name

            FROM signature_sessions

            JOIN signature_session_signer_assignments
                ON signature_sessions.id = signature_session_signer_assignments.signature_session_id
            JOIN signature_session_statuses
                ON signature_sessions.signature_session_status_id = signature_session_statuses.id
            JOIN users
                ON signature_sessions.author_id = users.id

            WHERE
                signature_session_signer_assignments.signer_id = ?
                AND
                signature_session_signer_assignments.signature_session_signer_status_id = {$this->signerStatuses->get('Ожидает подписания')}

            ORDER BY signature_sessions.created_at
            LIMIT ? OFFSET ?
        ", [$userId, $limit, $offset]);
    }


    public function getInWorkCount(int $userId): int
    {
        $result = $this->connection->selectOne("
            SELECT count(*)
            FROM (
                SELECT DISTINCT signature_sessions.id

                FROM signature_sessions

                JOIN signature_session_signer_assignments
                    ON signature_sessions.id = signature_session_signer_assignments.signature_session_id

                WHERE
                    (
                        -- Пользователь автор. Сессия в работе
                        signature_sessions.author_id = ?
                        AND
                        signature_sessions.signature_session_status_id = {$this->sessionStatuses->get('В работе')}
                    ) OR (
                        -- Пользователь подписант. Пользователь подписал. Сессия в работе
                        signature_session_signer_assignments.signer_id = ?
                        AND
                        signature_session_signer_assignments.signature_session_signer_status_id = {$this->signerStatuses->get('Подписано')}
                        AND
                        signature_sessions.signature_session_status_id = {$this->sessionStatuses->get('В работе')}
                    )
            ) AS tmp
        ", [$userId, $userId]);

        return $result->count;
    }


    public function getInWork(int $userId, int $limit, int $offset): array
    {
        $items = $this->connection->select("
            SELECT DISTINCT
                signature_sessions.id,
                signature_sessions.uuid,
                signature_sessions.title,
                signature_sessions.created_at,

                signature_session_statuses.id AS signature_session_status_id,
                signature_session_statuses.name AS signature_session_status_name,

                users.last_name AS author_last_name,
                users.first_name AS author_first_name,
                users.middle_name AS author_middle_name

            FROM signature_sessions

            JOIN signature_session_statuses
                ON signature_sessions.signature_session_status_id = signature_session_statuses.id
            JOIN signature_session_signer_assignments
                ON signature_sessions.id = signature_session_signer_assignments.signature_session_id
            JOIN users
                ON signature_sessions.author_id = users.id

            WHERE
                (
                    -- Пользователь автор. Сессия в работе
                    signature_sessions.author_id = ?
                    AND
                    signature_sessions.signature_session_status_id = {$this->sessionStatuses->get('В работе')}
                ) OR (
                    -- Пользователь подписант. Пользователь подписал. Сессия в работе
                    signature_session_signer_assignments.signer_id = ?
                    AND
                    signature_session_signer_assignments.signature_session_signer_status_id = {$this->signerStatuses->get('Подписано')}
                    AND
                    signature_sessions.signature_session_status_id = {$this->sessionStatuses->get('В работе')}
                )

            ORDER BY signature_sessions.created_at
            LIMIT ? OFFSET ?
        ", [$userId, $userId, $limit, $offset]);

        $items = Builder::create($items);

        if ($items->isEmpty()) {
            return [];
        }

        $ids = $items->pluck('id');

        $signers = $this->connection->select("
            SELECT
                signature_session_signer_assignments.signature_session_id,
                signature_session_signer_assignments.signature_session_signer_status_id,
                users.last_name,
                users.first_name,
                users.middle_name

            FROM signature_session_signer_assignments

            JOIN users
                ON signature_session_signer_assignments.signer_id = users.id

            WHERE signature_session_signer_assignments.signature_session_id IN ({$this->supporter->createPlaceholders($ids)})
        ", $ids);

        return $items->insertOuterItems(
            'id',
            'signers',
            $signers,
            'signature_session_id'
        )->toArray();
    }


    public function getFinishedCount(int $userId): int
    {
        $result = $this->connection->selectOne("
            SELECT count(*)
            FROM (
                SELECT DISTINCT signature_sessions.id

                FROM signature_sessions

                JOIN signature_session_signer_assignments
                    ON signature_sessions.id = signature_session_signer_assignments.signature_session_id

                WHERE
                    -- Пользователь автор или подписант. Сессия завершена
                    (
                        signature_sessions.author_id = ?
                        OR
                        signature_session_signer_assignments.signer_id = ?
                    )
                    AND
                    signature_sessions.signature_session_status_id = {$this->sessionStatuses->get('Завершена')}
            ) AS tmp
        ", [$userId, $userId]);

        return $result->count;
    }


    public function getFinished(int $userId, int $limit, int $offset): array
    {
        $items = $this->connection->select("
            SELECT DISTINCT
                signature_sessions.id,
                signature_sessions.uuid,
                signature_sessions.title,
                signature_sessions.created_at,
                signature_sessions.finished_at,

                users.last_name AS author_last_name,
                users.first_name AS author_first_name,
                users.middle_name AS author_middle_name

            FROM signature_sessions

            JOIN signature_session_signer_assignments
                ON signature_sessions.id = signature_session_signer_assignments.signature_session_id
            JOIN users
                ON signature_sessions.author_id = users.id

            WHERE
                -- Пользователь автор или подписант. Сессия завершена
                (
                    signature_sessions.author_id = ?
                    OR
                    signature_session_signer_assignments.signer_id = ?
                )
                AND
                signature_sessions.signature_session_status_id = {$this->sessionStatuses->get('Завершена')}

            ORDER BY signature_sessions.finished_at
            LIMIT ? OFFSET ?
        ", [$userId, $userId, $limit, $offset]);

        $items = Builder::create($items);

        if ($items->isEmpty()) {
            return [];
        }

        $ids = $items->pluck('id');

        $signers = $this->connection->select("
            SELECT
                signature_session_signer_assignments.signature_session_id,
                users.last_name,
                users.first_name,
                users.middle_name

            FROM signature_session_signer_assignments

            JOIN users
                ON signature_session_signer_assignments.signer_id = users.id

            WHERE signature_session_signer_assignments.signature_session_id IN ({$this->supporter->createPlaceholders($ids)})
        ", $ids);

        return $items->insertOuterItems(
            'id',
            'signers',
            $signers,
            'signature_session_id'
        )->toArray();
    }
}
