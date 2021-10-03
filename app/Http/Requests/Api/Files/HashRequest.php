<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Files;

use App\Http\Requests\AppFormRequest;
use App\Rules\SignatureAlgorithm;
use App\Rules\StarPaths\FilesystemStarPath;


/**
 * @property-read string $signatureAlgorithm
 * @property-read string $starPath
 */
final class HashRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'signatureAlgorithm' => ['bail', 'required', 'string', new SignatureAlgorithm()],
            'starPath'           => ['bail', 'required', 'string', new FilesystemStarPath()]
        ];
    }
}
