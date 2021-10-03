<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Vacations;

use App\Models\Vacations\Vacation;
use App\Rules\ModelExists;


/**
 * @property-read int $vacationId
 */
final class UpdateRequest extends CreateRequest
{

    public function rules(): array
    {
        return array_merge([
            'vacationId' => [
                'bail',
                'required',
                'integer',
                new ModelExists(Vacation::class, 'id')
            ]
        ], parent::rules());
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        if ($key == 'vacationId') {
            $value = (int) $value;
        } else {
            parent::mutateInputValue($key, $value);
        }
    }
}
