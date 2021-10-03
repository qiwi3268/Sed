<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Miscs;

use App\Http\Requests\AppFormRequest;
use App\Rules\Miscs\AliasMisc;


/**
 * @property-read string $alias
 */
final class SingleMiscRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'alias' => ['bail', 'required', 'string', new AliasMisc()]
        ];
    }
}
