<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\FileMapping\MappingCollection;
use App\Rules\Traits\ErrorDescription;


final class FileMapping implements Rule
{
    use ErrorDescription;


    public function passes($attribute, $value): bool
    {
        $mapping = (string) $value;

        return $this->handle(app(MappingCollection::class)->has($mapping), $mapping);
    }


    public function message(): string
    {
        return "Файловый маппинг: '$this->description' не существует";
    }
}
