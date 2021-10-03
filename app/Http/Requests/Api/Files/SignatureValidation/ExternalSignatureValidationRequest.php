<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Files\SignatureValidation;

use App\Http\Requests\AppFormRequest;
use App\Models\Files\File;
use App\Rules\ModelExists;
use App\Rules\StarPaths\FilesystemStarPath;


/**
 * @property-read string $originalStarPath
 */
final class ExternalSignatureValidationRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'originalStarPath' => [
                'bail',
                'required',
                'string',
                new FilesystemStarPath(),
                new ModelExists(File::class, 'star_path')
            ],
            // Успешно загруженный файл на сервер
            // Файл не пустой (больше 1 Кб)
            // Файл меньше 5 Мб
            'file' => ['bail', 'required', 'file', 'min:1', 'max:' . 5 * 1024]
        ];
    }
}
