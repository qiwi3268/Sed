<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;


abstract class AppFormRequest extends FormRequest
{
    /**
     * Проверка прерывается после первой ошибки
     */
    protected $stopOnFirstFailure = true;

    /**
     * Авторизация выполняется вне объекта запроса
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Валидация запроса, которая будет прервана после первой ошибки
     */
    public function stoppingValidate(array $rules): array
    {
        /** @var ValidationFactory $factory */
        $factory = $this->container->make(ValidationFactory::class);

        return $factory->make($this->all(), $rules)
            ->stopOnFirstFailure(true)
            ->validate();
    }


    /**
     * Мутирует параметры в зависимости от правил валидации
     *
     * @param string $key
     */
    public function __get($key)
    {
        $value = parent::__get($key);

        $this->mutateInputValue($key, $value);

        return $value;
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {}
}
