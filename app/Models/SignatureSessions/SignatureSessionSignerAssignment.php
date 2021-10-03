<?php

declare(strict_types=1);

namespace App\Models\SignatureSessions;

use DomainException;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AppModel;
use App\Models\User;


/**
 * @property-read SignatureSessionSignedFile|null $signedFile
 * @property-read User $signer
 * @mixin IdeHelperSignatureSessionSignerAssignment
 */
final class SignatureSessionSignerAssignment extends AppModel
{
    protected $casts = [
        'signed_at' => 'datetime'
    ];

    protected $fillable = [
        'signer_id',
    ];

    protected $attributes = [
        'signature_session_signer_status_id' => 1 // Ожидает подписания
    ];


    /**
     * Выполняет подписание
     *
     * @throws DomainException
     */
    public function sign(): self
    {
        if ($this->getRequiredAttribute('signature_session_signer_status_id') == 2) {
            throw new DomainException("Назначенный пользователь уже выполнил подписание");
        }

        $this->signed_at = now();
        $this->signature_session_signer_status_id = 2; // Подписано
        $this->update();
        return $this;
    }


    public function signedFile(): HasOne
    {
        return $this->hasOne(SignatureSessionSignedFile::class);
    }


    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signer_id');
    }
}
