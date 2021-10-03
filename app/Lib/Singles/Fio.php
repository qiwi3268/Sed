<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use InvalidArgumentException;
use Illuminate\Support\Str;


final class Fio
{
    private string $lastName;
    private string $firstName;
    private ?string $middleName;


    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $lastName, string $firstName, ?string $middleName)
    {
        if (
            !self::isValidPart($lastName)
            || !self::isValidPart($firstName)
            || (is_string($middleName) && !self::isValidPart($middleName))
        ) {
            throw new InvalidArgumentException('Некорректные данные ФИО');
        }

        $this->lastName = Str::ucfirst(Str::lower($lastName));
        $this->firstName = Str::ucfirst(Str::lower($firstName));
        $this->middleName = is_null($middleName) ? null : Str::ucfirst(Str::lower($middleName));
    }


    /**
     * Разбивает строку на массив из составляющих
     *
     * @return bool|array возвращает false в случае ошибки при разборе
     */
    public static function tokenizeString(string $fio): bool|array
    {
        $arr = explode(' ', $fio);

        $count = count($arr);

        if ($count != 3 && $count != 2) {
            return false;
        }

        foreach ($arr as $part) {

            if (!self::isValidPart($part)) {
                return false;
            }
        }
        $arr[2] ??= null;
        return $arr;
    }



    /**
     * Проверка части ФИО
     */
    public static function isValidPart(string $part): bool
    {
        return pm('/^[а-яё]+(-[а-яё]+)*$/iu', $part);
    }


    public function getLastName(): string
    {
        return $this->lastName;
    }


    public function getFirstName(): string
    {
        return $this->firstName;
    }


    public function hasMiddleName(): bool
    {
        return !is_null($this->middleName);
    }


    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }


    /**
     * Возвращает длинное ФИО
     */
    public function getLongFio(): string
    {
        $result = "$this->lastName $this->firstName";

        return is_null($this->middleName)
            ? $result
            :"$result $this->middleName";
    }


    /**
     * Возвращает короткое ФИО
     */
    public function getShortFio(): string
    {
        $result = $this->lastName . ' ' . mb_substr($this->firstName, 0, 1) . '.';
        return is_null($this->middleName)
            ? $result
            : $result . mb_substr($this->middleName, 0, 1) . '.';
    }


    public function __toString(): string
    {
        return $this->getLongFio();
    }
}
