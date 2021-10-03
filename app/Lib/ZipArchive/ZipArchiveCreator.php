<?php

declare(strict_types=1);

namespace App\Lib\ZipArchive;

use Symfony\Component\HttpFoundation\File\File;
use App\Lib\ZipArchive\Exceptions\ZipArchiveException;
use App\Lib\Filesystem\TmpFile\TmpFile;
use ZipArchive;


final class ZipArchiveCreator
{

    /**
     * Создаёт zip архив
     *
     * @param string $file абсолютный путь к создаваемому / перезаписываемому файлу архива
     * @throws ZipArchiveException
     */
    public function createArchive(string $file, FileBag $bag): File
    {
        $zip = new ZipArchive();

        $open = $zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($open !== true) {
            throw new ZipArchiveException("При открытии архива возникла ошибка: '$open'");
        }

        if ($bag->isEmpty()) {
            throw new ZipArchiveException("Получена пустая сумка с файлами");
        }

        foreach ($bag->getFiles() as [$path, $name]) {

            if ($zip->addFile($path, $name) !== true) {
                throw new ZipArchiveException("Ошибка при добавлении файла: '$path' в архив");
            }
        }

        if ($zip->close() !== true) {
            throw new ZipArchiveException("Ошибка при сохранении архива");
        }

        return new File($file);
    }


    /**
     * Создаёт временный zip архив
     *
     * @return File созданный временный файл
     */
    public function createTmpArchive(FileBag $bag): File
    {
        return $this->createArchive(
            (new TmpFile(false))->getPath(),
            $bag
        );
    }
}
