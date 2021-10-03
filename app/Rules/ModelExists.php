<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use App\Services\RequestValidation\ModelContainer;


/**
 * Устанавливает проверенные записи в ModelContainer
 */
final class ModelExists implements Rule
{
    private string $message;


    public function __construct(
        private string $className,
        private string $column,
        private ?string $salt = null
    ) {}


    public function passes($attribute, $value): bool
    {
        $value = (string) $value;

        /** @var Model $model */
        $model = new $this->className;

        $model = $model->where($this->column, '=', $value)->first();

        if (is_null($model)) {
            $this->message = "Модель: '$this->className' по условию: '$this->column = $value' не существует в БД";
            return false;
        }
        app(ModelContainer::class)->set($model, $value, $this->salt);
        return true;
    }


    public function message(): string
    {
        return $this->message;
    }
}
