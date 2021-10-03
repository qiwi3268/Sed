<?php

declare(strict_types=1);

namespace App\Lib\Filesystem\FileShell;

use App\Lib\Filesystem\FileShell\Exceptions\FileShellException;


/**
 * Является обёрткой для файловых функций
 *
 * Генерирует исключения, если возникают ошибки в стандартных функциях
 */
final class FileShell
{

    /**
     * @see filesize()
     * @throws FileShellException
     */
    public static function fileSize(string $path): int
    {
        $result = @filesize($path);

        if ($result === false) {
            throw new FileShellException("Ошибка при получении размера файла: '$path'");
        }
        return $result;
    }


    /**
     * @see unlink()
     * @throws FileShellException
     */
    public static function unlink(string $path): void
    {
        if (@unlink($path) === false) {
            throw new FileShellException("Ошибка при удалении файла: '$path'");
        }
    }


    /**
     * @see file_get_contents()
     * @throws FileShellException
     */
    public static function fileGetContents(string $path): string
    {
        $result = @file_get_contents($path);

        if ($result === false) {
            throw new FileShellException("Ошибка при получении содержимого файла: '$path'");
        }
        return $result;
    }


    /**
     * @see file_exists()
     */
    public static function fileExists(string $path): bool
    {
        return @file_exists($path);
    }
}
