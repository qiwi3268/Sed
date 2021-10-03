<?php

declare(strict_types=1);

namespace App\Rules\Traits;


trait ErrorDescription
{
    private string $description;


    /**
     * Обрабатывает результат правила валидации
     */
    private function handle(bool $result, string $description): bool
    {
        if (!$result) {
            $this->description = $description;
        }
        return $result;
    }
}
