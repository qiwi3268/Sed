<?php

declare(strict_types=1);

namespace App\Services\RequestValidation;

use LogicException;
use Illuminate\Database\Eloquent\Model;


/**
 * Используется в качестве контейнера с экземплярами моделей для сокращения обращений к БД
 */
final class ModelContainer
{
    /**
     * <pre>
     * [
     *     [
     *         'model' => object,
     *         'id'    => string,
     *         'salt'  => ?string
     *     ],
     *     ...
     * ]
     * </pre>
     */
    private array $container = [];


    /**
     * @param ?string $salt дополнительный параметр, который позволяет
     * различать модели одного класса с одинаковым id
     * @throws LogicException
     */
    public function set(Model $model, string $id, ?string $salt = null): void
    {
        $className = $model::class;

        if ($this->has($className, $id, $salt)) {

            $debug = is_null($salt)
                ? "Модель: '$className' с идентификатором: '$id'"
                : "Модель: '$className' с идентификатором: '$id' и солью: '$salt'";

            throw new LogicException("$debug уже присутствует в контейнере");
        }

        $this->container[] = [
            'model' => $model,
            'id'    => $id,
            'salt'  => $salt,
        ];
    }


    public function has(string $className, string $id, ?string $salt = null): bool
    {
        return arr_some(
            $this->container,
            fn (array $box) =>
                $box['model']::class == $className &&
                $box['id'] == $id &&
                $box['salt'] === $salt
        );
    }


    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function get(string $className, string $id, ?string $salt = null): Model
    {
        return arr_first(
            $this->container,
            fn (array $box) =>
                $box['model']::class == $className &&
                $box['id'] == $id &&
                $box['salt'] === $salt
        )['model'];
    }
}
