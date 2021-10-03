<?php

declare(strict_types=1);

namespace App\Models;


/**
 * @mixin IdeHelperPermission
 */
final class Permission extends AppModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'system_value'
    ];
}
