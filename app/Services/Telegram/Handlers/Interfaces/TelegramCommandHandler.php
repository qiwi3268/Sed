<?php

declare(strict_types=1);

namespace App\Services\Telegram\Handlers\Interfaces;

use WeStacks\TeleBot\Handlers\CommandHandler;


abstract class TelegramCommandHandler extends CommandHandler
{
    use TelegramHandlerTrait;


    protected function getCommandArgument(bool $normalizeSpaces = true): ?string
    {
        if (pm('/^\/.+\s+(.+)$/uU', $this->update->message->text, $matches)) {

            if (!$normalizeSpaces) {
                return $matches;
            }

            $normalized = [];

            foreach (explode(' ', $matches) as $word) {
                if ($word !== '') {
                    $normalized[] = $word;
                }
            }
            return implode(' ', $normalized);
        }
        return null;
    }
}
