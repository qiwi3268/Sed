<?php

declare(strict_types=1);

namespace App\Lib\Csp\Certification;

use App\Lib\Csp\Exceptions\LogicException;

use App\Lib\Singles\Fio;
use Webmozart\Assert\Assert;


/**
 * Представляет информации о личном сертификате:
 *
 * - Серийном номере (serial)
 * - Издателе (issuer)
 * - Владельце (subject)
 * - Дате и времени начала действия (notValidBefore)
 * - Дате и времени окончания действия (notValidAfter)
 *
 */
final class PersonalCertificate
{

    /**
     * @param string $notValidBefore формат d.m.Y H:i:s
     * @param string $notValidAfter формат d.m.Y H:i:s
     * @throws LogicException
     */
    public function __construct(
        private array $issuer,
        private array $subject,
        private string $serial,
        private string $notValidBefore,
        private string $notValidAfter,
    ) {
        Assert::allNotEmpty([$issuer, $subject, $serial, $notValidBefore, $notValidAfter]);

        if (!self::isPersonal($issuer, $subject)) {
            throw new LogicException('Сертификат не является личным');
        }
    }


    /**
     * Проверяет является ли сертификат личным
     */
    public static function isPersonal(array $issuer, array $subject): bool
    {
        // Минкомсвязь России выдает сертификаты только себе и удостоверяющим центрам
        if (str_contains(implode(' ', $issuer), 'Минкомсвязь России')) {
            return false;
        }
        // В сведения о владельце присутствуют данные о ФИО
        return array_key_exists('SN', $subject)
            && (array_key_exists('GN', $subject) || array_key_exists('G', $subject));
    }


    public function getSubjectFio(): Fio
    {
        $f = $this->subject['SN'];
        $io = explode(' ', $this->subject['GN'] ?? $this->subject['G']);

        [$i, $o] = count($io) == 2 ? $io : [$io[0], null];

        return new Fio($f, $i, $o);
    }


    public function getIssuer(): array { return $this->issuer; }

    public function getSubject(): array { return $this->subject; }

    public function getSerial(): string { return $this->serial; }

    public function getNotValidBefore(): string { return $this->notValidBefore; }

    public function getNotValidAfter(): string { return $this->notValidAfter; }
}
