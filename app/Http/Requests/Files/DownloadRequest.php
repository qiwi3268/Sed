<?php

declare(strict_types=1);

namespace App\Http\Requests\Files;

use App\Http\Requests\AppFormRequest;
use App\Rules\StarPaths\FilesystemStarPath;


/**
 * @property-read string $starPath
 */
final class DownloadRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'starPath' => ['bail', 'required', 'string', new FilesystemStarPath()]
        ];
    }
}
