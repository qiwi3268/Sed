<?php

declare(strict_types=1);

namespace App\Rules\Miscs;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Miscs\MiscModel;
use App\Lib\Miscs\SingleMiscManager;


final class FullMisc implements Rule
{
    private string $message;


    public function passes($attribute, $value): bool
    {
        $id = (int) $value;
        $alias = (string) $attribute;

        $manager = app(SingleMiscManager::class);

        if (!$manager->existsByAlias($alias)) {
            $this->message = "Алиас справочника: '$alias' не существует";
            return false;
        }

        /** @var MiscModel $misc */
        $misc = new ($manager->getClassNameByAlias($alias));

        if (!$misc->where('id', '=', $id)->exists()) {
            $this->message = "Справочник: '$alias' с id: '$id' отсутствует в БД";
            return false;
        }
        return true;
    }


    public function message(): string
    {
        return $this->message;
    }
}
