<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Guards;

use App\Http\Requests\AppFormRequest;
use App\Models\User;
use App\Rules\ModelExists;


/**
 * @property-read string $userUuid
 */
final class BaseGuardRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'userUuid' => [
                'bail',
                'required',
                'uuid',
                new ModelExists(User::class, 'uuid')
            ]
        ];
    }
}
