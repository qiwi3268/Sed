<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\MessageParsing\ParsingResult;


final class ParsingResultStructure
{
    public array $signerPrototypes = [];
    public ?string $commonError = null;
    public ?string $errorCode = null;
    public bool $result = false;
}
