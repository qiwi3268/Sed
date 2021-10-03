<?php

declare(strict_types=1);

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AppModel;


/**
 * @property-read Vacation $vacation
 * @mixin IdeHelperVacationReplacement
 */
final class VacationReplacement extends AppModel
{
    const UPDATED_AT = null;

    protected $fillable = [
        'replacement_user_id'
    ];


    public function vacation(): BelongsTo
    {
        return $this->belongsTo(Vacation::class);
    }
}
