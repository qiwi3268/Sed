<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\SignatureSessions\Guards;

use App\Http\Requests\AppFormRequest;
use App\Models\User;
use App\Models\SignatureSessions\SignatureSession;


/**
 * @property-read string $userUuid
 * @property-read string $signatureSessionUuid
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
                'exists:' . User::class . ',uuid'
            ],
            'signatureSessionUuid' => [
                'bail',
                'required',
                'uuid',
                'exists:' . SignatureSession::class . ',uuid'
            ]
        ];
    }
}
