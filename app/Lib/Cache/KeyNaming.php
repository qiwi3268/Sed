<?php

declare(strict_types=1);

namespace App\Lib\Cache;

use LogicException;
use InvalidArgumentException;
use JsonException;


/**
 * Единая точка для формирования ключей кэш хранилища
 */
final class KeyNaming
{


    /**
     * Генерирует ключ, по которому будут храниться кэшированные данные
     *
     * Принимаемые массивы должны быть конвертируемы в json
     *
     * @param array $who идентификация того, кто кэширует.
     * В общем случае - имя класса и метода, который выполняет менеджмент кэша.
     * @param array $what идентификация того, что кэшируется.
     * В общем случае - имя класса, метода и параметры, которые передаются в метод.
     * @throws InvalidArgumentException
     */
    public static function create(array $who, array $what): string
    {
        try {
            return json_encode($who, JSON_THROW_ON_ERROR) . '_' . json_encode($what, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException('Ошибка при преобразовании параметров в json', 0, $e);
        }
    }


    /**
     * Генерирует ключ для вызывающего метода
     *
     * @throws LogicException
     */
    public static function createForCaller(array $what): string
    {
        // Два последних вызова. Крайний из которых - текущий метод
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

        // Строго отслеживается наличие ключей, т.к. иначе возможна потеря кэша
        if (arr_all_key_exists($trace, ['class', 'function'])) {
            return self::create([$trace['class'], $trace['function']], $what);
        }
        throw new LogicException('В backtrace отсутствуют ключи class и/или function');
    }
}
