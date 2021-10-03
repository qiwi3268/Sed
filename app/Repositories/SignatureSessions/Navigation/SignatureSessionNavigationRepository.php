<?php

declare(strict_types=1);

namespace App\Repositories\SignatureSessions\Navigation;


interface SignatureSessionNavigationRepository
{
    public function getWaitingActionCount(int $userId): int;
    public function getWaitingAction(int $userId, int $limit, int $offset): array;

    public function getInWorkCount(int $userId): int;
    public function getInWork(int $userId, int $limit, int $offset): array;

    public function getFinishedCount(int $userId): int;
    public function getFinished(int $userId, int $limit, int $offset): array;
}
