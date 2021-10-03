<?php

declare(strict_types=1);

namespace App\Models;


/**
 * Объект, который может быть отображён в качестве справочника
 */
interface MiscPresentable
{

    public function getMiscLabel(): string;

    public function getMiscId(): int;
}
