<?php

declare(strict_types=1);

namespace App\Repositories\Files\Services;


interface FilesystemDeletionRepository
{

    /**
     * Возвращает массив starPath из входного параметра метода, которые отсутствуют в БД
     *
     * @param string[] $starPaths
     * @return string[]
     */
    public function getNotFoundStarPaths(array $starPaths): array;
}
