<?php

declare(strict_types=1);

namespace App\Models\Miscs;

use App\Models\AppModel;
use App\Models\MiscPresentable;


/**
 * Модель справочника
 */
abstract class MiscModel extends AppModel implements MiscPresentable
{
    public $timestamps = false;

    protected $casts = [
        'is_active' => 'bool'
    ];


    public function getMiscLabel(): string
    {
        return $this->getRequiredAttribute('name');
    }


    public function getMiscId(): int
    {
        return $this->getRequiredAttribute('id');
    }
}
