<?php

declare(strict_types=1);

namespace App\Repositories\Files\Services;


interface DatabaseManagementRepository
{
    public function updateNeeds(): int;

    public function updateNoNeeds(): int;

    public function deleteNoNeeds(): int;
}
