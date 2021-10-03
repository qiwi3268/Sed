<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Vacations;

use App\Http\Requests\AppFormRequest;
use App\Models\User;
use DateTimeImmutable;


/**
 * @property-read int $userId
 * @property-read DateTimeImmutable $startAt
 * @property-read int $duration
 * @property-read int[] $replacementIds
 */
class CreateRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'userId'           => ['bail', 'required', 'integer', 'exists:' . User::class . ',id'],
            'startAt'          => ['required', 'date_format:Y-m-d'],
            'duration'         => ['bail', 'required', 'integer', 'min:1'],
            'replacementIds'   => ['present', 'array'],
            'replacementIds.*' => [
                'bail',
                'required',
                'integer',
                'distinct',
                'different:userId',
                'exists:' . User::class . ',id'
            ]
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        switch ($key) {
            case 'userId':
            case 'duration':
                $value = (int) $value;
                break;
            case 'startAt':
                $value = resolve_date($value);
                break;
            case 'replacementIds':
                $value = arr_to_int($value);
                break;
        }
    }
}
