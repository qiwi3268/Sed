<?php

declare(strict_types=1);

namespace App\Lib\Csp\Certification\Commands;

use App\Lib\Csp\Command;
use Webmozart\Assert\Assert;


/**
 * Команда получения информации о сертификате
 */
final class CertificateInfoCommand
{

    /**
     * @param string $file абсолютный путь к файлу встроенной или открепленной подписи
     */
    public function __construct(private string $file)
    {
        Assert::fileExists($file);
    }


    public function getCommand(): array
    {
        return [
            Command::CERTMGR,
            '-list',         // показать сертификаты
            '-file',         // сертификаты из файла
            $this->file
        ];
    }
}
