<?php

declare(strict_types=1);

namespace App\Lib\Filesystem\TmpFile;

use RuntimeException;
use Closure;


final class TmpFile
{

    private string $path;

    private Closure $deleter;


    /**
     * @throws RuntimeException
     */
    public function __construct(private bool $deleteOnDestruct)
    {
        $path = tempnam(sys_get_temp_dir(), 'php');

        if (false === $path) {
            throw new RuntimeException('Ошибка при создании временного файла');
        }

        $this->deleter = static function () use ($path): void {
            @unlink($path);
        };

        $this->path = $path;

        register_shutdown_function($this->deleter);
    }


    public function getPath(): string
    {
        return $this->path;
    }


    public function __toString(): string
    {
        return $this->getPath();
    }


    public function __destruct()
    {
        if ($this->deleteOnDestruct) {
            ($this->deleter)($this->path);
        }
    }
}
