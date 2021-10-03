<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\SignatureSessions;

use App\Http\Requests\AppFormRequest;
use App\Models\SignatureSessions\SignatureSession;
use App\Models\Files\FileExternalSignature;
use App\Rules\StarPaths\FilesystemStarPath;
use App\Rules\ModelExists;
use App\Rules\Files\Csp\NotRedValidationResult;


/**
 * @property-read string $signatureSessionUuid
 * @property-read string $externalSignatureStarPath
 */
final class CreateSignRequest extends AppFormRequest
{
    public function rules(): array
    {
        return [
            'signatureSessionUuid' => [
                'bail',
                'required',
                'uuid',
                new ModelExists(SignatureSession::class, 'uuid')
            ],
            'externalSignatureStarPath' => [
                'bail',
                'required',
                'string',
                new FilesystemStarPath(),
                new ModelExists(FileExternalSignature::class, 'star_path'),
                new NotRedValidationResult(NotRedValidationResult::EXTERNAL_SIGNATURE)
            ]
        ];
    }
}
