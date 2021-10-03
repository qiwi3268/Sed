<?php

declare(strict_types=1);

namespace App\Models\Files;

use DomainException;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use App\Models\AppModel;
use App\Models\Traits\Casts\ValidationResultCast;
use App\Lib\Csp\SignatureValidation\Entities\ValidationResult;


/**
 * @property ValidationResult $validation_result
 * @property-read Collection|FileExternalSignature[] $externalSignatureFiles
 * @mixin IdeHelperFile
 */
final class File extends AppModel
{
    use ValidationResultCast;

    protected $casts = [
        'is_needs'     => 'bool',
        'cron_mark_at' => 'datetime'
    ];

    protected $fillable = [
        'user_id',
        'original_name',
        'size',
        'file_mapping',
        'star_path'
    ];

    protected $attributes = [
        'program_entity_id'   => null,
        'program_entity_type' => null,
        'is_needs'            => false
    ];


    /**
     * Прикрепляет файл к программной сущности и делает его "нужным"
     *
     * @throws DomainException
     */
    public function associateToProgramEntity(Model $programEntity): void
    {
        if (
            $this->getRequiredAttribute('program_entity_id') !== null
            || $this->getRequiredAttribute('program_entity_type') !== null
        ) {
            throw new DomainException('Файл уже прикреплён к программной сущности');
        }
        $this->programEntity()->associate($programEntity);
        $this->setNeeds()->save();
    }


    /**
     * Устанавливает файлу признак "нужности"
     *
     * @throws DomainException
     */
    public function setNeeds(): self
    {
        if ($this->getRequiredAttribute('is_needs') === true) {
            throw new DomainException('Файл уже имеет признак "нужности"');
        }
        $this->is_needs = true;
        $this->cron_mark_at = null;
        return $this;
    }


    public function programEntity(): MorphTo
    {
        return $this->morphTo('program_entity');
    }


    public function externalSignatureFiles(): HasMany
    {
        return $this->hasMany(FileExternalSignature::class);
    }
}
