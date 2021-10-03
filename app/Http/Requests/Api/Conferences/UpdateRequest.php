<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Conferences;

use App\Models\Conferences\Conference;
use App\Rules\ModelExists;


/**
 * @property-read string $conferenceUuid
 */
final class UpdateRequest extends CreateRequest
{

    public function rules(): array
    {
        return array_merge([
            'conferenceUuid' => [
                'bail',
                'required',
                'uuid',
                new ModelExists(Conference::class, 'uuid')
            ]
        ], parent::rules());
    }
}
