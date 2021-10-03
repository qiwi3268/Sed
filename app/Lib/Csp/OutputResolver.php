<?php

declare(strict_types=1);

namespace App\Lib\Csp;

use App\Lib\Csp\Exceptions\TechnicalException;
use Symfony\Component\Process\Process;


final class OutputResolver
{

    /**
     * Определяет поток с данными
     *
     * @throws TechnicalException
     */
    public static function resolve(Process $process): string
    {
        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();

        // Поток явно содержит ErrorCode или содержит любую строку, когда другой поток пуст
        if (str_icontains($stdout, 'ErrorCode') || ($stdout != '' && $stderr == '')) {
            return $stdout;
        } elseif (str_icontains($stderr, 'ErrorCode') || ($stderr != '' && $stdout == '')) {
            return $stderr;
        } else {
            throw new TechnicalException('Ошибка при обнаружении информационного потока');
        }
    }
}
