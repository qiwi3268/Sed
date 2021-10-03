<?php

declare(strict_types=1);

namespace App\Models\SignatureSessions;

use DomainException;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use App\Models\AppModel;
use App\Models\User;


/**
 * @property-read Collection|SignatureSessionSignerAssignment[] $signerAssignments
 * @property-read SignatureSession|null $zipArchive
 * @property-read User $author
 * @mixin IdeHelperSignatureSession
 */
final class SignatureSession extends AppModel
{
    protected $casts = [
        'finished_at' => 'datetime'
    ];

    protected $fillable = [
        'uuid',
        'author_id',
        'title',
        'file_id'
    ];

    protected $attributes = [
        'signature_session_status_id' => 1 // В работе
    ];


    public function signerAssignment(int $userId): SignatureSessionSignerAssignment
    {
        /** @var SignatureSessionSignerAssignment $result */
        $result = $this->signerAssignments()->where('signer_id', '=', $userId)->firstOrfail();
        return $result;
    }


    /**
     * Удаляет сессию подписания
     *
     * @throws DomainException
     */
    public function delete(): void
    {
        if ($this->getRequiredAttribute('signature_session_status_id') == 2) {
            throw new DomainException('Нельзя удалить завершённую сессию подписания');
        }
        parent::delete();
    }


    /**
     * Завершает сессию подписания
     *
     * @throws DomainException
     */
    public function finish(): void
    {
        if ($this->getRequiredAttribute('signature_session_status_id') == 2) {
            throw new DomainException('Сессия подписания уже завершена');
        }
        $this->finished_at = now();
        $this->signature_session_status_id = 2; // Завершена
        $this->update();
    }


    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }


    public function signerAssignments(): HasMany
    {
        return $this->hasMany(SignatureSessionSignerAssignment::class);
    }


    public function zipArchive(): HasOne
    {
        return $this->hasOne(SignatureSessionZipArchive::class);
    }
}
