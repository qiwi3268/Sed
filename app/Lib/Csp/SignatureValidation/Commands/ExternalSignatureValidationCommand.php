<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\Commands;

use App\Lib\Csp\Command;
use Webmozart\Assert\Assert;


/**
 * Команда проверки открепленной подписи
 */
final class ExternalSignatureValidationCommand implements SignatureValidationCommand
{

    /**
     * @param string $originalFile абсолютный путь к исходному файлу
     * @param string $externalSignatureFile абсолютный путь к файлу открепленной подписи
     */
    public function __construct(
        private string $originalFile,
        private string $externalSignatureFile
    ) {
        Assert::allFileExists([$originalFile, $externalSignatureFile]);
    }


    public function getChainCommand(): array
    {
        return [
            Command::CPROCSP,
            '-verify',
            '-detached',    // флаг открепленной подписи
            '-mca',         // поиск сертификатов осуществляется в хранилище компьютера CA
            '-all',         // использовать все найденные сертификаты
            '-errchain',    // завершать выполнение с ошибкой, если хотя бы один сертификат не прошел проверку
            '-verall',      // проверять все подписи
            $this->originalFile,
            $this->externalSignatureFile
        ];
    }


    public function getNoChainCommand(): array
    {
        return [
            Command::CPROCSP,
            '-verify',
            '-detached',    // флаг открепленной подписи
            '-nochain',     // не проверять цепочки найденных сертификатов
            '-verall',      // проверять все подписи
            $this->originalFile,
            $this->externalSignatureFile
        ];
    }


    public function getCryptographicFile(): string
    {
        return $this->externalSignatureFile;
    }
}
