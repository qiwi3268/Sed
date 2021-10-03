<?php

declare(strict_types=1);

namespace App\Services\Files\Uploading\Rules;

use LogicException;


abstract class Rule
{
    /**
     * Разрешена ли множественная загрузка
     */
    private bool $multipleAllowed;

    /**
     * Максимально допустимый размер в Мб
     */
    private int $maxSize;

    /**
     * Допустимые расширения
     */
    private ?array $allowableExtensions;

    /**
     * Запрещенные символы в наименовании
     */
    private ?array $forbiddenSymbols;


    public function __construct()
    {
        [
            $this->multipleAllowed,
            $maxSize,
            $this->allowableExtensions,
            $this->forbiddenSymbols
        ] = $this->getRules();

        if (is_null($maxSize)) {

            $ini = ini_get('upload_max_filesize');

            if (!pm('/^(\d+)M$/', $ini, $maxSize)) {
                throw new LogicException("Ошибка при определении максимального размера загружаемого файла: '$ini'");
            }
        }

        $this->maxSize = (int) $maxSize;
    }


    public function isMultipleAllowed(): bool { return $this->multipleAllowed; }

    public function getMaxSize(): int { return $this->maxSize; }

    public function getAllowableExtensions(): ?array { return $this->allowableExtensions; }

    public function getForbiddenSymbols(): ?array { return $this->forbiddenSymbols; }


    /**
     * Возвращает массив из 4х элементов:
     * <pre>
     * [
     *     bool,      // multipleAllowed
     *     ?int,      // maxSize
     *     ?string[], // allowableExtensions
     *     ?string[]  // forbiddenSymbols
     * ]
     * </pre>
     */
    abstract protected function getRules(): array;
}
