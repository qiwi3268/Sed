<?php

declare(strict_types=1);

namespace App\Lib\DatabaseMagicNumbers;

use App\Lib\DatabaseMagicNumbers\Exceptions\KeyInContainerDoesNotExistException;
use Webmozart\Assert\Assert;


final class MagicNumbersContainer
{
    private array $container = [];


    public function __construct(private string $name, array $data)
    {
        Assert::notEmpty($name);
        Assert::isNonEmptyMap($data);

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }


    private function set(string $key, int|string $value): void
    {
        $this->container[$key] = $value;
    }


    /**
     * Возвращает значение из контейнера
     *
     * @throws KeyInContainerDoesNotExistException
     */
    public function get(string $key): int|string
    {
        if (!array_key_exists($key, $this->container)) {
            throw new KeyInContainerDoesNotExistException($this->name, $key);
        }
        return $this->container[$key];
    }
}
