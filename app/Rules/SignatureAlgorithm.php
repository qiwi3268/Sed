<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\Csp\AlgorithmsList;
use App\Rules\Traits\ErrorDescription;


final class SignatureAlgorithm implements Rule
{
    use ErrorDescription;


    public function passes($attribute, $value): bool
    {
        $algorithm = (string) $value;

        return $this->handle(AlgorithmsList::existSignatureAlgorithm($algorithm), $algorithm);
    }


    public function message(): string
    {
        return "Алгоритм подписи: '$this->description' некорректен";
    }
}
