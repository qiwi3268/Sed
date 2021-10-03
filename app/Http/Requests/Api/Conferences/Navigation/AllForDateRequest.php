<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Conferences\Navigation;

use App\Http\Requests\AppFormRequest;
use DateTimeImmutable;


/**
 * @property-read DateTimeImmutable $date
 */
final class AllForDateRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'date' => ['bail', 'required', 'date_format:Y-m-d']
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        if ($key == 'date') {
            $value = resolve_date($value);
        }
    }
}
