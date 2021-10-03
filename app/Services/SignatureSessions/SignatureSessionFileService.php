<?php

declare(strict_types=1);

namespace App\Services\SignatureSessions;

use App\Repositories\SignatureSessions\SignatureSessionRepository;
use App\Lib\Filesystem\PrivateStorage\StarPath;
use App\Lib\ZipArchive\FileBag;


final class SignatureSessionFileService
{

    public function createFileBag(string $signatureSessionUuid): FileBag
    {
        $files = app(SignatureSessionRepository::class)->getFiles($signatureSessionUuid);

        $bag = new FileBag();

        $bag->add(
            (new StarPath($files['file']['starPath']))->getAbsolutePath(),
            $files['file']['name']
        );

        foreach ($files['externalSignatures'] as ['starPath' => $starPath, 'name' => $name]) {

            $bag->add(
                (new StarPath($starPath))->getAbsolutePath(),
                $name
            );
        }
        return $bag;
    }


    public function createArchiveName(int $signatureSessionId): string
    {
        return "Сессия_подписания_id_$signatureSessionId.zip";
    }
}
