<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Navigation;

use App\Http\Requests\AppFormRequest;


/**
 * @property-read int $page
 */
final class BaseNavigationRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'page' => ['required', 'integer'],
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        if ($key == 'page') {
            $value = (int) $value;
        }
    }
}
