<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\Entities;

use Webmozart\Assert\Assert;


final class ValidationResult
{
    private const GREEN_RESULT = 'green';
    private const ORANGE_RESULT = 'orange';
    private const RED_RESULT = 'red';

    private string $result;


    /**
     * @param Signer[] $signers
     */
    public function __construct(private array $signers)
    {
        Assert::notEmpty($signers);
        Assert::allIsInstanceOf($signers, Signer::class);

        $result = self::GREEN_RESULT;

        foreach ($signers as $signer) {

            if (!$signer->getSignatureResult()) {
                $result = self::RED_RESULT;
                break;
            }
            if (!$signer->getCertificateResult()) {
                $result = self::ORANGE_RESULT;
            }
        }
        $this->result = $result;
    }


    /**
     * @return Signer[]
     */
    public function getSigners(): array
    {
        return $this->signers;
    }


    public function getResult(): string
    {
        return $this->result;
    }


    public function isRedResult(): bool
    {
        return $this->result == self::RED_RESULT;
    }
}
