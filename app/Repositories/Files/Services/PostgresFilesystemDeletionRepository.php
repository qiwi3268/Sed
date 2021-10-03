<?php

declare(strict_types=1);

namespace App\Repositories\Files\Services;

use Illuminate\Database\PostgresConnection;
use App\Repositories\Utils\RepositorySupporter;


final class PostgresFilesystemDeletionRepository implements FilesystemDeletionRepository
{
    public function __construct(
        private PostgresConnection $connection,
        private RepositorySupporter $supporter
    ) {}


    /**
     * @param string[] $starPaths
     * @return string[]
     */
    public function getNotFoundStarPaths(array $starPaths): array
    {
        if (empty($starPaths)) {
            return [];
        }

        $result = $this->connection->select("
            WITH input_data (star_path) AS (
	            VALUES {$this->supporter->createPlaceholders($starPaths, '(%s)')}
            )
            SELECT input_data.star_path
            FROM input_data
            EXCEPT
                SELECT files.star_path
                FROM files
            EXCEPT
	            SELECT file_external_signatures.star_path
	            FROM file_external_signatures
        ", $starPaths);

        return array_column($result, 'star_path');
    }
}
