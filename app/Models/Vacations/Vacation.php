<?php

declare(strict_types=1);

namespace App\Models\Vacations;

use App\Models\AppModel;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * @property-read Collection|VacationReplacement[] $replacements
 * @mixin IdeHelperVacation
 */
final class Vacation extends AppModel
{
    protected $casts = [
        'start_at'  => 'date',
        'finish_at' => 'date'
    ];

    protected $fillable = [
        'author_id',
        'user_id',
        'start_at',
        'finish_at',
        'duration'
    ];


    public function replacements(): HasMany
    {
        return $this->hasMany(VacationReplacement::class);
    }
}
