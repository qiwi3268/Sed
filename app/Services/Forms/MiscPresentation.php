<?php

declare(strict_types=1);

namespace App\Services\Forms;

use App\Models\MiscPresentable;
use Traversable;


final class MiscPresentation
{

    public static function toResponse(Traversable|MiscPresentable|null $misc): ?array
    {
        if (is_null($misc)) {
            return null;
        } elseif ($misc instanceof Traversable) {
            return arr_map(fn (MiscPresentable $m): array => self::handleMisc($m), $misc);
        } else {
            return self::handleMisc($misc);
        }
    }


    private static function handleMisc(MiscPresentable $misc): array
    {
        return [
            'id'    => $misc->getMiscId(),
            'label' => $misc->getMiscLabel(),
        ];
    }
}
