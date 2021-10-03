<?php

declare(strict_types=1);

namespace App\Repositories\Conferences;

use App\Repositories\Exceptions\EmptyResponseRepositoryException;


interface ConferenceRepository
{

    /**
     * @throws EmptyResponseRepositoryException
     */
    public function get(string $conferenceUuid): array;

    /**
     * @throws EmptyResponseRepositoryException
     */
    public function getForUpdate(string $conferenceUuid): array;
}
