<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\SignatureSessions;

use App\Http\Requests\AppFormRequest;
use App\Models\SignatureSessions\SignatureSession;


/**
 * @property-read string $signatureSessionUuid
 */
final class ShowRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'signatureSessionUuid' => [
                'bail',
                'required',
                'uuid',
                'exists:' . SignatureSession::class . ',uuid'
            ]
        ];
    }
}
