<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Files;

use App\Http\Requests\AppFormRequest;


/**
 * @property-read string $starPath
 */
final class CheckRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'starPath' => ['required', 'string']
        ];
    }
}
