<?php

declare(strict_types=1);

namespace App\Models\Telegram\Polls;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

use App\Models\AppModel;


/**
 * @property-read TelegramPoll $poll
 * @property-read Collection|TelegramPollOptionAtWork[] $createdOptions
 * @mixin IdeHelperTelegramPollAtWork
 */
final class TelegramPollAtWork extends AppModel
{
    const UPDATED_AT = null;

    protected $casts = [
        'finished_at' => 'datetime'
    ];

    protected $attributes = [
        'telegram_poll_status_id' => 1 // Открыт
    ];


    /**
     * @return Builder|self self указано для подсказок IDE. По факту всегда возвращается Builder
     * @uses TelegramPollAtWork::poll()
     */
    public static function whereTgPollId(string $tgPollId): Builder|self
    {
        return self::whereHas('poll', function (Builder $b) use ($tgPollId): void {
            $b->where('tg_poll_id', '=', $tgPollId);
        });
    }


    public function poll(): BelongsTo
    {
        return $this->belongsTo(TelegramPoll::class, 'telegram_poll_id');
    }


    public function createdOptions(): BelongsToMany
    {
        return $this->belongsToMany(
            TelegramPollOptionAtWork::class, 'telegram_poll_created_option_at_works'
        );
    }
}
