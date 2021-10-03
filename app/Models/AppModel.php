<?php

declare(strict_types=1);

namespace App\Models;

use LogicException;
use Illuminate\Database\Eloquent\Model;


abstract class AppModel extends Model
{

    /**
     * Возвращает значение обязательного атрибута
     *
     * @throws LogicException
     */
    public function getRequiredAttribute(string $key): mixed
    {
        $attributes = $this->getAttributes();

        if (!array_key_exists($key, $attributes)) {
            $model = static::class;
            throw new LogicException("Обязательный атрибут по ключу: '$key' отсутствует в модели: '$model'");
        }
        return $this->getAttribute($key);
    }


    /**
     * Возвращает индексный массив значений обязательных атрибутов
     *
     * @param string[] $keys
     */
    public function getRequiredAttributes(array $keys): array
    {
        return array_map(fn (string $key): mixed => $this->getRequiredAttribute($key), $keys);
    }
}
