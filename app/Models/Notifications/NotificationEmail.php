<?php

declare(strict_types=1);

namespace App\Models\Notifications;

use App\Models\AppModel;


/**
 * @mixin IdeHelperNotificationEmail
 */
final class NotificationEmail extends AppModel
{
    const UPDATED_AT = null;

    protected $fillable = [
        'subject',
        'email'
    ];
}
