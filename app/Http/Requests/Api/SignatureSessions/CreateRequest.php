<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\SignatureSessions;

use App\Http\Requests\AppFormRequest;
use App\Models\User;
use App\Models\Files\File;
use App\Rules\StarPaths\FilesystemStarPath;
use App\Rules\ModelExists;


/**
 * @property-read string $title
 * @property-read int[] $signerIds
 * @property-read string $originalStarPath
 */
final class CreateRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'title'       => ['bail', 'required', 'string', 'max:200'],
            'signerIds'   => ['required', 'array'],
            'signerIds.*' => [
                'bail',
                'required',
                'integer',
                'distinct',
                'exists:' . User::class . ',id'
            ],
            'originalStarPath' => [
                'bail',
                'required',
                'string',
                new FilesystemStarPath(),
                new ModelExists(File::class, 'star_path')
            ]
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        if ($key == 'signerIds') {
            $value = arr_to_int($value);
        }
    }
}
