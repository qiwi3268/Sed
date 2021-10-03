<?php

declare(strict_types=1);

namespace App\Lib\Csp\Hashing;

use App\Lib\Csp\Exceptions\UnexpectedMessageException;
use App\Lib\Csp\Exceptions\TechnicalException;

use App\Lib\Csp\Hashing\Commands\HashCommand;
use App\Lib\Csp\MessageParser;
use App\Lib\Csp\OutputResolver;
use App\Lib\Filesystem\FileShell\FileShell;
use Symfony\Component\Process\Process;


final class HashGetter
{
    private MessageParser $messageParser;


    public function __construct(private HashCommand $hashCommand)
    {
        $this->messageParser = new MessageParser();
    }


    /**
     * Возвращает хэш файла
     *
     * @throws UnexpectedMessageException
     * @throws TechnicalException
     */
    public function getHash(): string
    {
        // По сравнению с проверкой подписи - процесс получения хэша файла является очень простым.
        // Чтобы лишний раз не усложнять программный код, все операции
        // производятся в этом методе; без выделения дополнительных абстракций и т.д.

        $process = new Process($this->hashCommand->getCommand());
        $process->run();
        $message = OutputResolver::resolve($process);

        if (!$this->messageParser->isOkErrorCode($message)) {
            throw new UnexpectedMessageException('Код ошибки не соответствует успешному выполнению команды', $message);
        }

        $hashDir = $this->hashCommand->getHashDir();

        if (!str_ends_with($hashDir, '/')) {
            $hashDir .= '/';
        }

        $hashFile = $hashDir . basename($this->hashCommand->getFile()) . '.hsh';

        if (!FileShell::fileExists($hashFile)) {
            throw new TechnicalException("В ФС сервера отсутствует сгенерированный hash файл: '$hashFile'");
        }

        $hash = FileShell::fileGetContents($hashFile);
        FileShell::unlink($hashFile);

        return $hash;
    }
}
