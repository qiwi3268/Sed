<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\OutputMessage;

use App\Lib\Csp\SignatureValidation\Commands\SignatureValidationCommand;
use App\Lib\Csp\Certification\Commands\CertificateInfoCommand;
use App\Lib\Csp\OutputResolver;
use Symfony\Component\Process\Process;


final class CommandLineOutputMessenger implements OutputMessenger
{
    private CertificateInfoCommand $certificateInfoCommand;

    private Process $chainProcess;
    private Process $noChainProcess;
    private Process $certificateProcess;


    public function __construct(private SignatureValidationCommand $signatureValidationCommand)
    {
        $this->certificateInfoCommand = new CertificateInfoCommand($signatureValidationCommand->getCryptographicFile());
    }


    public function run(): void
    {
        // Инициализация процессов
        $this->chainProcess = new Process($this->signatureValidationCommand->getChainCommand());
        $this->noChainProcess = new Process($this->signatureValidationCommand->getNoChainCommand());
        $this->certificateProcess = new Process($this->certificateInfoCommand->getCommand());

        // Запуск процессов
        $this->chainProcess->start();
        $this->noChainProcess->start();
        $this->certificateProcess->start();
    }


    public function getChainMessage(): string
    {
        $this->chainProcess->wait();
        return OutputResolver::resolve($this->chainProcess);
    }


    public function getNoChainMessage(): string
    {
        $this->noChainProcess->wait();
        return OutputResolver::resolve($this->noChainProcess);
    }


    public function getCertificateInfoMessage(): string
    {
        $this->certificateProcess->wait();
        return OutputResolver::resolve($this->certificateProcess);
    }
}
