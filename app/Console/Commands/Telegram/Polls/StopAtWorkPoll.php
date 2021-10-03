<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram\Polls;

use Illuminate\Console\Command;
use App\Repositories\Telegram\Polls\AtWorkPollRepository;
use App\Services\Telegram\UseCases\AtWorkPollUseCase;


final class StopAtWorkPoll extends Command
{

    protected $signature = 'telegram_poll:stop_at_work';

    protected $description = 'Закрывает опрос "На работе"';


    public function handle(AtWorkPollRepository $repository, AtWorkPollUseCase $useCase): int
    {
        $openPoll = $repository->getOpenPollByDate(now());

        $useCase->stopPoll($openPoll->telegram_poll_at_work_id, $openPoll->tg_message_id);

        $this->info('Опрос успешно закрыт');
        return 0;
    }
}
