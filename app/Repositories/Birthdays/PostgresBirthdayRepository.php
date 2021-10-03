<?php

declare(strict_types=1);

namespace App\Repositories\Birthdays;

use Illuminate\Database\PostgresConnection;
use App\Repositories\Utils\RepositorySupporter;
use DateTimeInterface;


final class PostgresBirthdayRepository implements BirthdayRepository
{
    public function __construct(
        private PostgresConnection $connection,
        private RepositorySupporter $supporter
    ) {}


    public function getAll(): array
    {
        return $this->connection->select("
            SELECT
                last_name,
                first_name,
                middle_name,
                date_of_birth

            FROM users

            ORDER BY date_part('month', date_of_birth), date_part('day', date_of_birth), last_name
        ");
    }


    /**
     * @param DateTimeInterface[] $dates
     */
    public function getByDatesOfBirth(array $dates): array
    {
        if (empty($dates)) {
            return [];
        }

        $values = array_map(
            fn (DateTimeInterface $d) => "{$d->format('n')}-{$d->format('j')}",
            $dates
        );

        return $this->connection->select("
            SELECT
                last_name,
                first_name,
                middle_name,
                date_of_birth

            FROM users

            WHERE concat(date_part('month', date_of_birth), '-', date_part('day', date_of_birth)) IN ({$this->supporter->createPlaceholders($values)})

            ORDER BY date_part('month', date_of_birth), date_part('day', date_of_birth), last_name
        ", $values);
    }
}
