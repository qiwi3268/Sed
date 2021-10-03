<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Conferences\Navigation;

use App\Http\Requests\AppFormRequest;


/**
 * @property-read int $year
 */
final class DatesWithConferencesForYearRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'year'  => ['bail', 'required', 'integer', 'between:2000,3000']
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        if ($key == 'year') {
            $value = (int) $value;
        }
    }
}
