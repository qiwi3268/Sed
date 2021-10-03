<?php

declare(strict_types=1);

namespace App\Events\SignatureSessions;

use Illuminate\Database\Eloquent\Collection;
use App\Events\AppEvent;
use App\Models\SignatureSessions\SignatureSession;
use App\Models\SignatureSessions\SignatureSessionSignerAssignment;


final class SignatureSessionCreated extends AppEvent
{
    /**
     * @var SignatureSession модель со всеми полями
     */
    public SignatureSession $signatureSession;

    public Collection $signers;


    public function __construct(SignatureSession $signatureSession)
    {
        /**
         * @uses SignatureSession::$signerAssignments
         * @uses SignatureSessionSignerAssignment::$signer
         */
        $signatureSession->loadMissing('signerAssignments.signer');

        $this->signers = $signatureSession->signerAssignments->map->signer;

        $this->signatureSession = $signatureSession->withoutRelations();
    }
}
