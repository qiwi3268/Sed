<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Vacations;

use App\Http\Requests\AppFormRequest;


/**
 * @property-read int $year
 * @property-read int $month
 */
final class ShowForYearAndMonthRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'year'  => ['bail', 'required', 'integer', 'between:2000,3000'],
            'month' => ['bail', 'required', 'integer', 'between:1,12'],
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        if ($key == 'year' || $key == 'month') {
            $value = (int) $value;
        }
    }
}
