<?php

declare(strict_types=1);

namespace App\Events\SignatureSessions;

use App\Events\AppEvent;
use App\Models\SignatureSessions\SignatureSession;


final class SignatureSessionFinished extends AppEvent
{

    /**
     * @param SignatureSession $signatureSession модель со всеми полями
     */
    public function __construct(public SignatureSession $signatureSession)
    {}
}
