<?php

declare(strict_types=1);

namespace App\Models\Telegram;

use App\Models\AppModel;


/**
 * @mixin IdeHelperTelegramAccount
 */
final class TelegramAccount extends AppModel
{
    const UPDATED_AT = null;

    protected $fillable = [
        'tg_user_id'
    ];
}
