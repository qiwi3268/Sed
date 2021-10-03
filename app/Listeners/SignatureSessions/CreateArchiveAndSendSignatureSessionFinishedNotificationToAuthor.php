<?php

declare(strict_types=1);

namespace App\Listeners\SignatureSessions;

use Throwable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Lib\Filesystem\PrivateStorage\StarPath;
use App\Lib\Filesystem\PrivateStorage\PrivateStorageManager;
use App\Lib\ZipArchive\ZipArchiveCreator;
use App\Lib\RouteSynchronization\RouteSynchronizer;
use App\Events\SignatureSessions\SignatureSessionFinished;
use App\Notifications\SignatureSessions\SignatureSessionFinishedNotification;
use App\Models\Files\File;
use App\Services\SignatureSessions\SignatureSessionFileService;


final class CreateArchiveAndSendSignatureSessionFinishedNotificationToAuthor implements ShouldQueue
{
    use Queueable;


    public function __construct(
        private PrivateStorageManager $storage,
        private SignatureSessionFileService $fileService,
        private ZipArchiveCreator $archiveCreator,
        private RouteSynchronizer $routeSynchronizer
    ) {}


    /**
     * @throws Throwable
     */
    public function handle(SignatureSessionFinished $event): void
    {
        $signatureSession = $event->signatureSession;

        $path = $this->storage->getAbsolutePath($this->storage->getFreeHashName());
        $fileBag = $this->fileService->createFileBag($signatureSession->uuid);

        $archiveFile = $this->archiveCreator->createArchive($path, $fileBag);
        $archiveFileName = $this->fileService->createArchiveName($signatureSession->id);

        DB::transaction(function () use ($signatureSession, $archiveFileName, $archiveFile): void {

            $file = File::create([
                // Создателем файла назначается автор сессии подписания
                'user_id'       => $signatureSession->author_id,
                'original_name' => $archiveFileName,
                'size'          => $archiveFile->getSize(),
                'star_path'     => StarPath::createFromAbsolutePath($archiveFile->getPathname())
            ]);

            $signatureSessionZipArchive = $signatureSession->zipArchive()->create([
                'file_id' => $file->id
            ]);

            $file->associateToProgramEntity($signatureSessionZipArchive);
        });

        $url = $this->routeSynchronizer->generateAbsoluteUrl(
            'signatureSession.view',
            ['uuid' => $signatureSession->uuid]
        );

        Notification::send($signatureSession->author, new SignatureSessionFinishedNotification($url, $archiveFile->getPathname(), $archiveFileName));
    }
}
