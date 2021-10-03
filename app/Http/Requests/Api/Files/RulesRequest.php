<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Files;

use App\Rules\FileMapping;
use App\Http\Requests\AppFormRequest;


/**
 * @property-read string $mapping
 */
final class RulesRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'mapping' => ['bail', 'required', 'string', new FileMapping()]
        ];
    }
}
