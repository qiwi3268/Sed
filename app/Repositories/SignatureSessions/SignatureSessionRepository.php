<?php

declare(strict_types=1);

namespace App\Repositories\SignatureSessions;

use App\Repositories\Exceptions\EmptyResponseRepositoryException;


interface SignatureSessionRepository
{

    /**
     * @throws EmptyResponseRepositoryException
     */
    public function get(string $signatureSessionUuid): object;


    /**
     * @throws EmptyResponseRepositoryException
     */
    public function getForSigning(string $signatureSessionUuid): object;


    /**
     * Может ли пользователь подписать сессию подписания
     *
     * Не проверяет существование пользователя и сессии подписания
     */
    public function canUserSign(string $userUuid, string $signatureSessionUuid): bool;


    /**
     * Может ли пользователь удалить сессию подписания
     *
     * Не проверяет существование пользователя и сессии подписания
     */
    public function canUserDelete(string $userUuid, string $signatureSessionUuid): bool;


    /**
     * Все ли назначенные пользователи подписали
     *
     * Не проверяет существование сессии подписания
     */
    public function isAllSigned(string $signatureSessionUuid): bool;


    /**
     * Возвращает массив созданных открепленных подписей и подписываемый файл
     *
     * @return array
     * <pre>
     * [
     *     file => [
     *         starPath => ...
     *         name => ...
     *     ],
     *     externalSignatures => [
     *         [
     *             starPath => ...
     *             name => ...
     *         ],
     *         [
     *             starPath => ...
     *             name => ...
     *         ],
     *         ...
     *     ]
     * ]
     * </pre>
     * @throws EmptyResponseRepositoryException
     */
    public function getFiles(string $signatureSessionUuid): array;
}
