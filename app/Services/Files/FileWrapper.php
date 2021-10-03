<?php

declare(strict_types=1);

namespace App\Services\Files;

use InvalidArgumentException;
use App\Models\Files\File;


/**
 * Обёртка для добавления дополнительных полей к файлу
 */
final class FileWrapper
{
    private array $fields = [];

    public function __construct(private File $file)
    {}


    /**
     * @throws InvalidArgumentException
     */
    public function addField(string $key, mixed $value): self
    {
        if (array_key_exists($key, $this->fields)) {
            throw new InvalidArgumentException("Поле по ключу: '$key' уже добавлено к файлу");
        }
        $this->fields[$key] = $value;
        return $this;
    }


    public function getFields(): array
    {
        return $this->fields;
    }


    public function getFileModel(): File
    {
        return $this->file;
    }
}
