<?php

declare(strict_types=1);

namespace App\Rules\Files;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use App\Rules\Traits\ErrorDescription;
use Webmozart\Assert\Assert;


final class ForbiddenSymbols implements Rule
{
    use ErrorDescription;


    public function __construct(private array $forbiddenSymbols)
    {
        Assert::notEmpty($forbiddenSymbols);
        Assert::allString($forbiddenSymbols);
    }


    /**
     * @param $value UploadedFile
     */
    public function passes($attribute, $value): bool
    {
        $name = $value->getClientOriginalName();

        return $this->handle(!str_contains_any($name, $this->forbiddenSymbols), $name);
    }


    public function message(): string
    {
        return "Файл: '$this->description' имеет в названии один из запрещённых символов: '" . implode(' / ', $this->forbiddenSymbols) . "'";
    }
}
