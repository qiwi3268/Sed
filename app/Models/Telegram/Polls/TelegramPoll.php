<?php

declare(strict_types=1);

namespace App\Models\Telegram\Polls;

use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\AppModel;


/**
 * @property-read TelegramPollAtWork|null $atWorkPoll
 * @mixin IdeHelperTelegramPoll
 */
final class TelegramPoll extends AppModel
{
    const UPDATED_AT = null;

    protected $fillable = [
        'tg_message_id',
        'tg_from_user_id',
        'tg_chat_id',
        'tg_poll_id'
    ];


    public function pollAtWork(): HasOne
    {
        return $this->hasOne(TelegramPollAtWork::class);
    }
}
