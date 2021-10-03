<?php

declare(strict_types=1);

namespace App\Console\Commands\Files\Services;

use Illuminate\Console\Command;
use App\Models\Files\Services\FileServiceFilesystemDeletion;
use App\Repositories\Files\Services\FilesystemDeletionRepository;
use App\Lib\Filesystem\FileShell\FileShell;
use App\Lib\Filesystem\PrivateStorage\StarPath;
use App\Lib\Filesystem\PrivateStorage\StarPathPacker;


final class FilesystemDeletion extends Command
{

    protected $signature = 'file_service:filesystem_deletion';

    protected $description = 'Удаляет файлы в файловой системе сервера, которые отсутствуют в файловых таблицах';


    public function handle(StarPathPacker $packer, FilesystemDeletionRepository $repository): int
    {
        $count = 0;

        foreach ($packer->getGenerator() as $pack) {

            foreach ($repository->getNotFoundStarPaths($pack) as $starPath) {

                $path = (new StarPath($starPath))->getAbsolutePath();

                $size = FileShell::fileSize($path);

                FileShell::unlink($path);

                FileServiceFilesystemDeletion::create([
                    'absolute_path' => $path,
                    'size'          => $size
                ]);

                $count++;
            }
        }
        $this->info("Удалено $count файла(ов)");
        return 0;
    }
}
