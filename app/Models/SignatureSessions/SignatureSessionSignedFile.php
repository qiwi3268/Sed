<?php

declare(strict_types=1);

namespace App\Models\SignatureSessions;

use App\Models\AppModel;


/**
 * @mixin IdeHelperSignatureSessionSignedFile
 */
final class SignatureSessionSignedFile extends AppModel
{
    protected $fillable = [
        'file_external_signature_id',
    ];
}
