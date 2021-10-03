<?php

declare(strict_types=1);

namespace App\Http\Requests\Files\SignatureSessions;

use App\Http\Requests\AppFormRequest;
use App\Models\SignatureSessions\SignatureSession;
use App\Rules\ModelExists;


/**
 * @property-read string $signatureSessionUuid
 */
final class DownloadGeneratedZipRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'signatureSessionUuid' => [
                'bail',
                'required',
                'uuid',
                new ModelExists(SignatureSession::class, 'uuid')
            ]
        ];
    }
}
