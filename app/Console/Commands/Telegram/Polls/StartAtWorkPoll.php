<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram\Polls;

use Throwable;
use Illuminate\Console\Command;
use App\Services\Telegram\UseCases\AtWorkPollUseCase;


final class StartAtWorkPoll extends Command
{

    protected $signature = 'telegram_poll:start_at_work';

    protected $description = 'Запускает опрос "На работе"';


    /**
     * @throws Throwable
     */
    public function handle(AtWorkPollUseCase $useCase): int
    {
        $useCase->startPoll();
        $this->info('Опрос успешно открыт');
        return 0;
    }
}
