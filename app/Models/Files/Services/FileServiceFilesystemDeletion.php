<?php

declare(strict_types=1);

namespace App\Models\Files\Services;

use App\Models\AppModel;


/**
 * @mixin IdeHelperFileServiceFilesystemDeletion
 */
final class FileServiceFilesystemDeletion extends AppModel
{
    const CREATED_AT = 'processed_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'absolute_path',
        'size'
    ];
}
