<?php

declare(strict_types=1);

namespace App\Models\Files;

use DomainException;

use App\Models\AppModel;
use App\Models\Traits\Casts\ValidationResultCast;
use App\Lib\Csp\SignatureValidation\Entities\ValidationResult;


/**
 * @property ValidationResult $validation_result
 * @mixin IdeHelperFileExternalSignature
 */
final class FileExternalSignature extends AppModel
{
    use ValidationResultCast;

    protected $casts = [
        'is_needs'     => 'bool',
        'cron_mark_at' => 'datetime'
    ];

    protected $fillable = [
        'user_id',
        'star_path',
        'validation_result',
        'created_name',
        'size'
    ];

    protected $attributes = [
        'is_needs' => false
    ];


    /**
     * Устанавливает файлу признак "нужности"
     *
     * @throws DomainException
     */
    public function setNeeds(): self
    {
        if ($this->getRequiredAttribute('is_needs') === true) {
            throw new DomainException('Файл откреплённой подписи уже имеет признак "нужности"');
        }
        $this->is_needs = true;
        $this->cron_mark_at = null;
        return $this;
    }
}
