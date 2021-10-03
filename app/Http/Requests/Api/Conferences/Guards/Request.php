<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Conferences\Guards;

use App\Http\Requests\AppFormRequest;
use App\Models\Conferences\Conference;
use App\Models\User;
use App\Rules\ModelExists;


/**
 * @property-read string $userUuid
 * @property-read string $conferenceUuid
 */
final class Request extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'userUuid' => [
                'bail',
                'required',
                'uuid',
                new ModelExists(User::class, 'uuid')
            ],
            'conferenceUuid' => [
                'bail',
                'required',
                'uuid',
                new ModelExists(Conference::class, 'uuid')
            ]
        ];
    }
}
