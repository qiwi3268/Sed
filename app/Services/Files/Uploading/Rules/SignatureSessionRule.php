<?php

declare(strict_types=1);

namespace App\Services\Files\Uploading\Rules;


final class SignatureSessionRule extends Rule
{

    protected function getRules(): array
    {
        return [
            RulesLibrary::MULTIPLE_FORBIDDEN,
            RulesLibrary::MAX_FILE_SIZES[0],
            RulesLibrary::ALLOWABLE_EXTENSIONS[0],
            RulesLibrary::FORBIDDEN_SYMBOLS[0]
        ];
    }
}
