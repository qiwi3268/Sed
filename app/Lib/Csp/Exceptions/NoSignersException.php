<?php

declare(strict_types=1);

namespace App\Lib\Csp\Exceptions;


/**
 * В сообщении отсутствуют подписанты
 * Связан с проверкой некорректных файлов
 */
final class NoSignersException extends CspException
{}
