<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\UseCases\MorningGreetingUseCase;
use Illuminate\Console\Command;


final class SendMorningGreeting extends Command
{

    protected $signature = 'telegram:send_morning_greeting';

    protected $description = 'Отправляет утреннее приветствие';


    public function handle(MorningGreetingUseCase $useCase): int
    {
        $useCase->sendMessage();
        $this->info('Утреннее приветствие успешно отправлено');
        return 0;
    }
}
