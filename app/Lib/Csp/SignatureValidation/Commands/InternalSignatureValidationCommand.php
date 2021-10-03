<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\Commands;

use App\Lib\Csp\Command;
use Webmozart\Assert\Assert;


/**
 * Команда проверки встроенной подписи
 */
final class InternalSignatureValidationCommand implements SignatureValidationCommand
{

    /**
     * @param string $internalSignatureFile абсолютный путь к файлу встроенной подписи
     * @param string|null $unpackedFile абсолютный путь, по которому будет создан файл,
     * извлечённый из встроенной подписи. Если null, то файл не будет извлечён
     */
    public function __construct(
        private string $internalSignatureFile,
        private ?string $unpackedFile = null
    ) {
        Assert::fileExists($internalSignatureFile);
    }


    public function getChainCommand(): array
    {
        return [
            Command::CPROCSP,
            '-verify',
            '-attached',    // флаг встроенной подписи
            '-mca',         // поиск сертификатов осуществляется в хранилище компьютера CA
            '-all',         // использовать все найденные сертификаты
            '-errchain',    // завершать выполнение с ошибкой, если хотя бы один сертификат не прошел проверку
            '-verall',      // проверять все подписи
            $this->internalSignatureFile,
        ];
    }

    /*
     * Извлечение файла происходит только при проверке подписи без цепочки сертификатов,
     * поскольку необходим [ErrorCode: 0x00000000].
     * Если, например, истекло время действия личного сертификата, то при проверке с цепочкой сертификатов
     * будет ошибка [ErrorCode: 0x200001f9]. Такой файл не будет извлечён.
     * Если подпись физически верна, то проверка без цепочки сертификатов всегда выполняется с [ErrorCode: 0x00000000].
     */

    public function getNoChainCommand(): array
    {
        $command = [
           Command::CPROCSP,
            '-verify',
            '-attached',    // флаг встроенной подписи
            '-nochain',     // не проверять цепочки найденных сертификатов
            '-verall',      // проверять все подписи
            $this->internalSignatureFile,
        ];

        if (!is_null($this->unpackedFile)) {
            $command[] = $this->unpackedFile;
        }
        return $command;
    }


    public function getCryptographicFile(): string
    {
        return $this->internalSignatureFile;
    }
}
