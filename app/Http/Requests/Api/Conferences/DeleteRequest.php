<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Conferences;

use App\Http\Requests\AppFormRequest;
use App\Models\Conferences\Conference;
use App\Rules\ModelExists;


/**
 * @property-read string $conferenceUuid
 */
final class DeleteRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'conferenceUuid' => [
                'bail',
                'required',
                'uuid',
                new ModelExists(Conference::class, 'uuid')
            ]
        ];
    }
}
