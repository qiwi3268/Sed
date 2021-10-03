<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Telegram\Polls;

use App\Http\Requests\AppFormRequest;
use DateTimeImmutable;


/**
 * @property-read DateTimeImmutable $date
 */
final class ShowForDateRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'date' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'before_or_equal:now'
            ]
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        if ($key == 'date') {
            $value = resolve_date($value);
        }
    }
}
