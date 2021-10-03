<?php

declare(strict_types=1);

namespace App\Lib\Filesystem\PrivateStorage;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Filesystem\FilesystemAdapter;


final class PrivateDisk
{
    public const FILESYSTEM_DISK = 'private';

    public function __construct(private FilesystemManager $manager)
    {}

    public static function getAdapter(): FilesystemAdapter
    {
        return app(self::class)->manager->disk(self::FILESYSTEM_DISK);
    }
}
