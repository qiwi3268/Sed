<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\MessageParsing\ParsingResult;

use Webmozart\Assert\Assert;


final class SignerPrototype
{

    public function __construct(private string $info, private string $message, private int $id)
    {
        Assert::notEmpty($info);
        Assert::notEmpty($message);
    }

    public function getInfo(): string { return $this->info; }

    public function getMessage(): string { return $this->message; }

    public function getId(): int { return $this->id; }
}
