<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\MessageParsing\ParsingResult;

use InvalidArgumentException;
use App\Lib\Csp\Exceptions\LogicException;

use Webmozart\Assert\Assert;


final class ParsingResult
{
    /**
     * @var SignerPrototype[]
     */
    private array $signerPrototypes;
    private ?string $commonError;
    private ?string $errorCode;
    public bool $result;


    /**
     * @param ParsingResultStructure $structure позволяет сделать этот класс иммутабельным,
     * избавив его от сеттеров
     */
    public function __construct(ParsingResultStructure $structure)
    {
        Assert::allIsInstanceOf($this->signerPrototypes = $structure->signerPrototypes, SignerPrototype::class);
        Assert::nullOrNotEmpty($this->commonError = $structure->commonError);
        Assert::nullOrNotEmpty($this->errorCode = $structure->errorCode);
        $this->result = $structure->result;
    }


    public function hasSignerPrototypes(): bool
    {
        return !empty($this->signerPrototypes);
    }


    public function hasOnlyErrorCode(): bool
    {
        return empty($this->signerPrototypes)
            && is_null($this->commonError)
            && !is_null($this->errorCode);
    }


    /**
     * Возвращает прототип подписанта из текущего массива по id, который совпадает
     * с id другого прототипа
     *
     * @throws LogicException
     */
    public function getIdenticalSigner(SignerPrototype $otherSignerPrototype): SignerPrototype
    {
        $otherId = $otherSignerPrototype->getId();
        try {
            return arr_first($this->signerPrototypes, fn (SignerPrototype $s): bool => $s->getId() == $otherId);
        } catch (InvalidArgumentException) {
            throw new LogicException("Прототип подписанта с id: '$otherId' отсутствует");
        }
    }


    public function getSignerPrototypes(): array { return $this->signerPrototypes; }

    public function getCommonError(): ?string { return $this->commonError; }

    public function getErrorCode(): ?string { return $this->errorCode; }

    public function getResult(): bool { return $this->result; }
}
