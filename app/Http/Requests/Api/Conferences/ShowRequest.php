<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Conferences;

use App\Http\Requests\AppFormRequest;
use App\Models\Conferences\Conference;


/**
 * @property-read string $conferenceUuid
 */
final class ShowRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'conferenceUuid' => [
                'bail',
                'required',
                'uuid',
                'exists:' . Conference::class . ',uuid'
            ]
        ];
    }
}
