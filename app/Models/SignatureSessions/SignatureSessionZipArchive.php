<?php

declare(strict_types=1);

namespace App\Models\SignatureSessions;

use App\Models\AppModel;


/**
 * @mixin IdeHelperSignatureSessionZipArchive
 */
final class SignatureSessionZipArchive extends AppModel
{
    const UPDATED_AT = null;

    protected $fillable = [
        'file_id',
    ];
}
