<?php

declare(strict_types=1);

namespace App\Models\Files\Services;

use App\Models\AppModel;


/**
 * @mixin IdeHelperFileServiceDatabaseManagement
 */
final class FileServiceDatabaseManagement extends AppModel
{
    const CREATED_AT = 'processed_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'updated_needs',
        'updated_no_needs',
        'deleted_no_needs'
    ];
}
