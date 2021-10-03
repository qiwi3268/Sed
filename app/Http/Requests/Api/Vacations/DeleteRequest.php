<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Vacations;

use App\Http\Requests\AppFormRequest;
use App\Models\Vacations\Vacation;
use App\Rules\ModelExists;


/**
 * @property-read int $vacationId
 */
final class DeleteRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'vacationId' => [
                'bail',
                'required',
                'integer',
                new ModelExists(Vacation::class, 'id')
            ]
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        if ($key == 'vacationId') {
            $value = (int) $value;
        }
    }
}
