<?php

declare(strict_types=1);

namespace App\Lib\Csp;


/**
 * Список алгоритмов подписи и хэширования
 */
final class AlgorithmsList
{
    /**
     * Алгоритмы подписи
     */
    public const SIGNATURE_ALGORITHMS = [
        '1.2.643.2.2.19'    => '1.2.643.2.2.19',    // Алгоритм ГОСТ Р 34.10-2001, используемый при экспорте/импорте ключей
        '1.2.643.7.1.1.1.1' => '1.2.643.7.1.1.1.1', // Алгоритм ГОСТ Р 34.10-2012 для ключей длины 256 бит, используемый при экспорте/импорте ключей
        '1.2.643.7.1.1.1.2' => '1.2.643.7.1.1.1.2', // Алгоритм ГОСТ Р 34.10-2012 для ключей длины 512 бит, используемый при экспорте/импорте ключей
    ];


    /**
     * Алгоритмы хэширования
     * Соответствие алгоритмов хэширования к алгоритмам подписи
     */
    public const HASH_ALGORITHMS = [
        self::SIGNATURE_ALGORITHMS['1.2.643.2.2.19']    => '1.2.643.2.2.9',     // Функция хэширования ГОСТ Р 34.11-94
        self::SIGNATURE_ALGORITHMS['1.2.643.7.1.1.1.1'] => '1.2.643.7.1.1.2.2', // Функция хэширования ГОСТ Р 34.11-2012, длина выхода 256 бит
        self::SIGNATURE_ALGORITHMS['1.2.643.7.1.1.1.2'] => '1.2.643.7.1.1.2.3'  // Функция хэширования ГОСТ Р 34.11-2012, длина выхода 512 бит
    ];


    public static function existSignatureAlgorithm(string $signatureAlgorithm): bool
    {
        return array_key_exists($signatureAlgorithm, self::SIGNATURE_ALGORITHMS);
    }


    public static function existHashAlgorithm(string $hashAlgorithm): bool
    {
        return in_array($hashAlgorithm, self::HASH_ALGORITHMS);
    }
}
