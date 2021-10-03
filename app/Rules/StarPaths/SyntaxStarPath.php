<?php

declare(strict_types=1);

namespace App\Rules\StarPaths;

use App\Services\StarPathValidation\Exceptions\StarPathValidationException;

use Illuminate\Contracts\Validation\Rule;
use App\Services\StarPathValidation\StarPathValidator;


final class SyntaxStarPath implements Rule
{
    private string $message;


    public function passes($attribute, $value): bool
    {
        $starPath = (string) $value;
        $validator = new StarPathValidator($starPath);

        try {
            $validator->validateSyntax();
            return true;
        } catch (StarPathValidationException $e) {
            $this->message = $e->getMessage();
            return false;
        }
    }


    public function message(): string
    {
        return $this->message;
    }
}
