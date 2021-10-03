<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


abstract class AppEvent
{
    use Dispatchable, SerializesModels;
}
