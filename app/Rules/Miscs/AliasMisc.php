<?php

declare(strict_types=1);

namespace App\Rules\Miscs;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\Miscs\SingleMiscManager;
use App\Rules\Traits\ErrorDescription;


final class AliasMisc implements Rule
{
    use ErrorDescription;


    public function passes($attribute, $value): bool
    {
        $alias = (string) $value;

        return $this->handle(app(SingleMiscManager::class)->existsByAlias($alias), $alias);
    }


    public function message(): string
    {
        return "Алиас справочника: '$this->description' не существует";
    }
}
