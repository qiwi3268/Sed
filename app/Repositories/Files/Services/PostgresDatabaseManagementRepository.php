<?php

declare(strict_types=1);

namespace App\Repositories\Files\Services;

use Illuminate\Database\PostgresConnection;
use Webmozart\Assert\Assert;


final class PostgresDatabaseManagementRepository implements DatabaseManagementRepository
{
    /**
     * @param int $deleteAfter через сколько часов можно удалять "ненужные" записи
     * относительно cron_mark_at
     */
    public function __construct(private int $deleteAfter, private PostgresConnection $connection)
    {
        Assert::greaterThan($deleteAfter, 0);
    }


    public function updateNeeds(): int
    {
        $count = 0;

        $count += $this->connection->update("
            UPDATE files
            SET cron_mark_at = NULL
            WHERE
                is_needs = TRUE
                AND
                cron_mark_at IS NOT NULL
        ");

        $count += $this->connection->update("
            UPDATE file_external_signatures
            SET cron_mark_at = NULL
            WHERE
                is_needs = TRUE
                AND
                cron_mark_at IS NOT NULL
        ");

        return $count;
    }


    public function updateNoNeeds(): int
    {
        $count = 0;

        $count += $this->connection->update("
            UPDATE files
            SET cron_mark_at = now()
            WHERE
                is_needs = FALSE
                AND
                cron_mark_at IS NULL
        ");

        $count += $this->connection->update("
            UPDATE file_external_signatures
            SET cron_mark_at = now()
            WHERE
                is_needs = FALSE
                AND
                cron_mark_at IS NULL
        ");

        return $count;
    }


    public function deleteNoNeeds(): int
    {
        $count = 0;

        $count += $this->connection->delete("
            DELETE
            FROM files
            WHERE
                is_needs = FALSE
                AND
                cron_mark_at IS NOT NULL
                AND
                now() >= (cron_mark_at + make_interval(hours => $this->deleteAfter))
        ");

        $count += $this->connection->delete("
            DELETE
            FROM file_external_signatures
            WHERE
                is_needs = FALSE
                AND
                cron_mark_at IS NOT NULL
                AND
                now() >= (cron_mark_at + make_interval(hours => $this->deleteAfter))
        ");

        // Счётчик может не отображать фактическое число удалённых записей, потому что
        // в него не входят каскадно удаленные записи из таблицы file_external_signatures
        return $count;
    }
}
